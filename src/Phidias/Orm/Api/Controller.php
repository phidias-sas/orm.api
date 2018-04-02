<?php
namespace Phidias\Orm\Api;

use Phidias\Orm\Api\Entity\Controller as EntityController;

class Controller
{
    protected $settings;

    public function __construct($settings)
    {
        $this->settings = $settings;
    }

    public function collection()
    {
        $collection = EntityController::factory($this->settings->entity);

        if (isset($this->settings->query)) {
            if (isset($this->settings->query->attributes)) {
                $collection->attributes($this->settings->query->attributes);
            }

            if (isset($this->settings->query->where)) {
                $collection->where($this->settings->query->where);
            }

            if (isset($this->settings->query->fetch)) {
                return $collection->fetch($this->settings->query->fetch);
            }
        }

        $collection->limit(50);

        return $collection;
    }


    public function single()
    {
        $collection = EntityController::factory($this->settings->entity);

        if (isset($this->settings->query->attributes)) {
            $collection->attributes($this->settings->query->attributes);
        } else {
            $collection->allAttributes();
        }

        return $collection->fetch($this->settings->id);
    }    
}