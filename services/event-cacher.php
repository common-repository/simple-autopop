<?php

class Simple_AutoPOP_EventCacher
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var int
     */
    private $expireMin;

    /**
     * Simple_AutoPOP_EventCacher constructor.
     * @param $enabled
     * @param int $expireMin
     */
    public function __construct($enabled, $expireMin = 5)
    {
        $this->enabled = $enabled;
        $this->expireMin = $expireMin;
    }

    public static function getCacheKey($eventNumber)
    {
        return Simple_AutoPOP::PREFIX . '_cache_' . $eventNumber;
    }

    /**
     * @param $eventNumber
     * @return null
     */
    public function get($eventNumber)
    {
        if (!$this->enabled) {
            return null;
        }

        if ($this->expireMin <= 0) {
            return null;
        }

        $value = get_option(self::getCacheKey($eventNumber));
        if (!$value) {
            return null;
        }

        if (!isset($value['expire'])) {
            return null;
        }
        $now = new \DateTime();
        $expire = new \DateTime($value['expire']);
        if ($now->getTimestamp() > $expire->getTimestamp()) {
            return null;
        }

        return $value['data'];
    }

    /**
     * @param $data
     * @param $eventNumber
     */
    public function set($data, $eventNumber)
    {
        $now = new \DateTime();
        $expire = $now->add(new DateInterval('PT' . $this->expireMin . 'M'));
        $value = [
            'expire' => $expire->format('Y-m-d H:i:s'),
            'data' => $data
        ];
        update_option(self::getCacheKey($eventNumber), $value);
    }

}