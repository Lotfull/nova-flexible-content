<?php

namespace Whitecube\NovaFlexibleContent\Value;

class Resolver implements ResolverInterface
{

    /**
     * Set the field's value
     *
     * @param  mixed  $model
     * @param  string $attribute
     * @param  Illuminate\Support\Collection $groups
     * @return string
     */
    public function set($model, $attribute, $groups)
    {
        $modelAttribute = $model->$attribute;
        return $model->$attribute = $groups->map(function($group) use ($modelAttribute) {

            // Merge old attributes with the new ones to avoid override File/ Image Fields when nested Images
            if(isset($modelAttribute)){
                foreach ($modelAttribute as $oldAttribute) {
                    if($oldAttribute['key'] == $group->key()) {
                        return [
                            'layout' => $group->name(),
                            'key' => $group->key(),
                            'attributes' => array_merge($oldAttribute['attributes'], $group->getAttributes())
                        ];
                    }
                }
            }

            return [
                'layout' => $group->name(),
                'key' => $group->key(),
                'attributes' => $group->getAttributes()
            ];
        });
    }

    /**
     * get the field's value
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @param  Whitecube\NovaFlexibleContent\Layouts\Collection $layouts
     * @return Illuminate\Support\Collection
     */
    public function get($resource, $attribute, $layouts)
    {
        $value = $this->extractValueFromResource($resource, $attribute);

        return collect($value)->map(function($item) use ($layouts) {
            $layout = $layouts->find($item->layout);

            if(!$layout) return;

            return $layout->duplicateAndHydrate($item->key, (array) $item->attributes);
        })->filter();
    }

    /**
     * Find the attribute's value in the given resource
     *
     * @param  mixed  $resource
     * @param  string $attribute
     * @return array
     */
    protected function extractValueFromResource($resource, $attribute)
    {
        $value = data_get($resource, str_replace('->', '.', $attribute)) ?? [];

        if (is_string($value)) $value = json_decode($value) ?? [];

        // Fail silently in case data is invalid
        if (!is_array($value)) return [];

        return array_map(function($item) {
            return is_array($item) ? (object) $item : $item;
        }, $value);
    }

}
