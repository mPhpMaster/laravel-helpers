<?php
/*
 * Copyright © 2022. mPhpMaster(https://github.com/mPhpMaster) All rights reserved.
 */

if ( !function_exists('modelToQuery') ) {
    /**
     * @param \Model $model
     *
     * @return \Model|\Illuminate\Database\Eloquent\Builder
     */
    function modelToQuery($model)
    {
        $_model = $model->newQuery();
        /** @var $_model \Model */
        return $_model->whereKey($model->getKey());
    }
}

if ( !function_exists('getModelKey') ) {
    /**
     * Returns Model Key Only!
     *
     * @param $object
     *
     * @return mixed|object|int
     */
    function getModelKey($object)
    {
        if ( isModel($object) ) {
            $key = $object->getKeyName() ?: 'id';
            if ( !($return = ($object->getKey() ?: $object->{$key})) ) {
                $return = object_get($object, $key) ?: array_get($object->toArray(), $key);
            }

            if ( $return ) {
                return $return;
            }
        }

        return $object;
    }
}

if ( !function_exists('getModel') ) {
    /**
     * Returns model of query|model.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    function getModel($model)
    {
        try {
            if ( is_object($model) ) {
                return $model->getModel();
            }
        } catch (Exception $exception) {
            try {
                if ( is_object($model) ) {
                    return $model->getQuery()->getModel();
                }
            } catch (Exception $exception2) {

            }
        }

        return null;
    }
}
