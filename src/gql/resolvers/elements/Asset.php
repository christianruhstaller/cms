<?php
namespace craft\gql\resolvers\elements;

use craft\db\Table;
use craft\elements\Asset as AssetElement;
use craft\helpers\Db;
use craft\helpers\Gql as GqlHelper;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Class Asset
 */
class Asset extends BaseElement
{
    /**
     * @inheritdoc
     */
    public static function prepareQuery($source, array $arguments, $fieldName = null)
    {
        // If this is the beginning of a resolver chain, start fresh
        if ($source === null) {
            $query = AssetElement::find();
        // If not, get the prepared element query
        } else {
            $query = $source->$fieldName;
        }

        // If it's preloaded, it's preloaded.
        if (is_array($query)) {
            return $query;
        }

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        $pairs = GqlHelper::extractAllowedEntitiesFromToken('read');

        if (!GqlHelper::canQueryAssets()) {
            return [];
        }

        $query->andWhere(['in', 'assets.volumeId', array_values(Db::idsByUids(Table::VOLUMES, $pairs['volumes']))]);

        return $query;
    }
}
