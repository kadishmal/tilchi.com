<?php
/**
 * LanguageUrlRule is for matching Language abbreviations in the URL.
 * Ex. /site/ky/phrase, where "ky" should be in the database matching
 * some language abbreviation.
 */
class LanguageUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';

    public function createUrl($manager,$route,$params,$ampersand)
    {
        if ($route === '/site/view')
        {
            if (isset($params['language'], $params['phrase']))
                return $params['language'] . '/' . $params['phrase'];
            else if (isset($params['language']))
                return $params['language'];
        }
        return false;  // this rule does not apply
    }

    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
		// matches site/ky/phrase
        if (preg_match('%^site/(\w+)(/(.+)(/)?)?$%', $pathInfo, $matches))
        {
			$connection = Yii::app()->db;
			$command = $connection->createCommand('SELECT id FROM tbl_languages WHERE abbreviation = :abbreviation');
			$command->bindParam(':abbreviation', $matches[1]);
			$results = $command->queryRow();

			if ($results)
			{
				$_GET['language'] = $results['id'];
				$_GET['phrase'] = $matches[3];
				return '/site/view';
			}
        }
        return false;  // this rule does not apply
    }
}