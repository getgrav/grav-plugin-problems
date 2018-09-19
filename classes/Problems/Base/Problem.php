<?php
namespace Grav\Plugin\Problems\Base;

class Problem implements \JsonSerializable
{
    const LEVEL_CRITICAL = 'critical';
    const LEVEL_WARNING = 'warning';
    const LEVEL_NOTICE = 'notice';
    
    protected $id;
    protected $order;
    protected $level;
    protected $status;
    protected $msg;
    protected $details;
    protected $help;
    
    public function process() {
        return $this;
    }

    public function getId() {
        return $this->id;
    }

    public function getOrder() {
        return $this->order;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getMsg() {
        return $this->msg;
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function getHelp()
    {
        return $this->help;
    }

    public function toArray()
    {
        return get_object_vars($this);
    }

    public function jsonSerialize()
    {
        $this->toArray();
    }
}