<?php
/**
 * LanguageUrlRule is for matching Language abbreviations in the URL.
 * Ex. /site/ky/phrase, where "ky" should be in the database matching
 * some language abbreviation.
 */
class LanguageUrlRule extends CBaseUrlRule
{
    public $connectionID = 'db';
    /**
     * @var array the default GET parameters (name=>value) that this rule provides.
     * When this rule is used to parse the incoming request, the values declared in this property
     * will be injected into $_GET.
     */
    public $defaultParams=array();
    /**
     * @var array list of parameters (name=>regular expression)
     */
    public $params=array();

    public function createUrl($manager,$route,$params,$ampersand)
    {
        if ($route === '/site/view')
        {
            $url = '';

            if (isset($params['fromLang']))
            {
                $url .= $params['fromLang'];

                if (isset($params['toLang']))
                {
                    $url .= '/' . $params['toLang'];

                    if (isset($params['phrase']))
                    {
                        $url .= '/' . $params['phrase'];
                    }
                }
            }

            return $url;
        }
        return false;  // this rule does not apply
    }

    public function parseUrl($manager,$request,$pathInfo,$rawPathInfo)
    {
		// matches site/ky/ru/phrase
        if (preg_match('%^site/(\w+)(/(\w+)(/(.+)(/)?)?)?$%', $pathInfo, $matches))
        {
			$connection = Yii::app()->db;
			$command = $connection->createCommand('SELECT id FROM tbl_languages WHERE abbreviation = :abbreviation');
			$command->bindParam(':abbreviation', $matches[1]);
			$results = $command->queryRow();
            $uri = '';

			if ($results)
			{
                Yii::app()->request->cookies['Tilchi_fromLang'] = new CHttpCookie('Tilchi_fromLang', $results['id']);
                $_GET['fromLang'] = $results['id'];
                $uri = '/site/index';

                if (isset($matches[3]))
                {
                    $command->bindParam(':abbreviation', $matches[3]);
                    $results = $command->queryRow();

                    if ($results)
                    {
                        Yii::app()->request->cookies['Tilchi_toLang'] = new CHttpCookie('Tilchi_toLang', $results['id']);
                        $_GET['toLang'] = $results['id'];

                        if (isset($matches[5]))
                        {
                            $_GET['phrase'] = $matches[5];

                            foreach($this->defaultParams as $name=>$value)
                            {
                                if(!isset($_GET[$name]))
                                    $_REQUEST[$name]=$_GET[$name]=$value;
                            }

                            $uri = '/site/view';
                        }
                    }
                }

                return $uri;
            }
        }
        return false;  // this rule does not apply
    }
}