<?php

namespace common\components;

use common\models\ActiveQuery;
use common\models\ActiveRecord;
use yii\base\Behavior;
use Httpful\Request;
use yii\base\Event;
use yii\base\ModelEvent;

class PubSubBehavior extends Behavior
{

    public $db = 'db';
    public $subscriptionClass = '\common\models\Subscription';

    public $headers = [];

    public $params = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterInsert',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }


    private function getRequest($method)
    {
        $request = Request::init($method);
        $request->addHeaders($this->headers);

        return $request;
    }

    /**
     * @param \common\models\Subscription $subscription
     * @param ModelEvent $event
     * @return \Httpful\Response
     */
    private function publish($subscription, $event)
    {
        /** @var ActiveRecord $model */
        $model = $event->sender;

        $eventNames = [];
        foreach ($subscription->events as $name) {
            $eventNames['after' . ucfirst($name)] = $name;
        }

        if (!empty($subscription->events) && in_array($event->name, array_keys($eventNames))) {

            if (empty($subscription->models) || (!empty($subscription->models) && in_array(get_class($event->sender),
                        $subscription->models))
            ) {

                $queryParams = http_build_query(array_merge($this->params, [
                    'id' => $model->id,
                    'model' => get_class($event->sender),
                    'event' => $eventNames[$event->name],
                    'data' => $event->data,
                    'attributes' => json_encode($event->sender->attributes),
                ]));

                $url = http_build_url($subscription->url, [
                    'query' => $queryParams,
                ]);

                try {
                    /** @var \Httpful\Response $response */
                    $response = $this->getRequest($subscription->requestMethod)->uri($url)->send();
                    return $response;

                } catch (\Exception $e) {
                    // ignore it
                }

            }

        }
        return null;
    }

    /**
     * @param Event $event
     */
    public function afterFind($event)
    {
        /** @var ActiveQuery $query */
        $query = call_user_func([$this->subscriptionClass, 'find']);

        $query->where(['enabled' => true]);

        /** @var \common\models\Subscription $subscription */
        foreach ($query->all() as $subscription) {
            $this->publish($subscription, $event);
        }

    }

    public function afterDelete($event)
    {
        /** @var ActiveQuery $query */
        $query = call_user_func([$this->subscriptionClass, 'find']);

        $query->where(['enabled' => true]);

        /** @var \common\models\Subscription $subscription */
        foreach ($query->all() as $subscription) {
            $this->publish($subscription, $event);
        }
    }

    public function afterInsert($event)
    {
        /** @var ActiveQuery $query */
        $query = call_user_func([$this->subscriptionClass, 'find']);

        $query->where(['enabled' => true]);

        /** @var \common\models\Subscription $subscription */
        foreach ($query->all() as $subscription) {
            $this->publish($subscription, $event);
        }
    }

    public function afterUpdate($event)
    {
        /** @var ActiveQuery $query */
        $query = call_user_func([$this->subscriptionClass, 'find']);

        $query->where(['enabled' => true]);

        /** @var \common\models\Subscription $subscription */
        foreach ($query->all() as $subscription) {
            $this->publish($subscription, $event);
        }
    }


}