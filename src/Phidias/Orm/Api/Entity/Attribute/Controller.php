<?php
namespace Phidias\Orm\Api\Entity\Attribute;

use Phidias\Orm\Api\Entity\Attribute\Entity as Attribute;

class Controller
{
    public static function collection($entityId)
    {
        return Attribute::collection()
            ->allAttributes()
            ->match('entity', $entityId);
    }

    public static function save($entityId, $attributeData, $attributeId = null)
    {
        $attribute = new Attribute($attributeId);
        $attribute->setValues($attributeData);
        $attribute->entity = $entityId;
        $attribute->mysql = isset($incomingAttributeData->mysql) && $incomingAttributeData->mysql ? json_encode($incomingAttributeData->mysql) : null;
        $attribute->save();

        return $attribute;
    }

    public static function get($entityId, $attributeId)
    {
        return Attribute::single()
            ->allAttributes()
            ->match("entity", $entityId)
            ->fetch($attributeId);
    }

    public static function delete($entityId, $attributeId)
    {
        $attribute = Attribute::single()
            ->allAttributes()
            ->match("entity", $entityId)
            ->fetch($attributeId);

        $attribute->delete();

        return $attribute;
    }

    public static function saveAll($entityId, $incomingAttributes)
    {
        if (!is_array($incomingAttributes)) {
            $incomingAttributes = array($incomingAttributes);
        }

        $existing = Attribute::collection()
            ->allAttributes()
            ->match("entity", $entityId)
            ->find();

        $deleteTargets = [];
        foreach ($existing as $existingAttribute) {
            $deleteTargets[$existingAttribute->id] = $existingAttribute->id;
        }

        $collection = Attribute::collection()
            ->allAttributes()
            ->set("entity", $entityId);

        /* Create new attributes */
        foreach ($incomingAttributes as $incomingAttributeData) {
            $attribute = new Attribute;
            $attribute->setValues($incomingAttributeData);
            $attribute->mysql = isset($incomingAttributeData->mysql) && $incomingAttributeData->mysql ? json_encode($incomingAttributeData->mysql) : null;

            $collection->add($attribute);

            if (isset($incomingAttributeData->id)) {
                unset($deleteTargets[$incomingAttributeData->id]);
            }
        }

        $collection->save();

        /* Delete remaining attributes */
        if (count($deleteTargets)) {
            Attribute::collection()
                ->match("id", $deleteTargets)
                ->delete();
        }

        return Attribute::collection()
            ->allAttributes()
            ->match("entity", $entityId)
            ->addFilter(function($attribute) {
                $attribute->mysql = json_decode($attribute->mysql);
            })
            ->find()
            ->fetchAll();
    }

}
