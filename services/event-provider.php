<?php

class Simple_AutoPOP_EventProvider
{
    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $token;

    /**
     * @var Simple_AutoPOP_EventCacher
     */
    private $eventCacher;

    /**
     * @var number
     */
    private $eventNumber;

    /**
     * @return number
     */
    public function getEventNumber()
    {
        return $this->eventNumber;
    }

    /**
     * @param number $eventNumber
     * @return Simple_AutoPOP_EventProvider
     */
    public function setEventNumber($eventNumber)
    {
        $this->eventNumber = $eventNumber;
        return $this;
    }

    /**
     * Simple_AutoPOP_EventProvider constructor.
     * @param $apiUrl
     * @param $token
     */
    public function __construct($apiUrl, $token, $eventCacher)
    {
        $this->apiUrl = $apiUrl;
        $this->token = $token;
        $this->eventCacher = $eventCacher;
    }

    /**
     * @return string
     */
    private function generateUrl()
    {
        return $this->apiUrl . '?method=event_calendar.list&token=' . $this->token . '&count=' . $this->eventNumber;
    }

    /**
     * @param $eventResponse
     * @return array
     */
    private function parseEventResponse($eventResponse)
    {
        $content = json_decode($eventResponse, true);
        if (!$content) {
            return [];
        }

        if (!isset($content['status'])) {
            return [];
        }

        if ($content['status'] !== 'success') {
            return [];
        }

        $result = [];
        foreach($content['response'] as $key => $item) {
            if (is_numeric($key) && is_array($item)) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        $url = $this->generateUrl();

        if ($events = $this->eventCacher->get($this->eventNumber)) {
            return $events;
        }

        $content = file_get_contents($url);
        $events = $this->parseEventResponse($content);
        if (!$content) {
            return array();
        }
        $this->eventCacher->set($events, $this->eventNumber);


        return $events;
    }
}
