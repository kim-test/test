<?php

/**
 * 自定义Tag
 *
 * Shine支持两种自定义tag类型: simpletag和inclusiontag。
 *   simpletag是普通的tag，如{% now %}，其实就是一个函数调用，所以只要声明callback就行了。
 *
 *   inclusiontag用来包含另外一个模板，这个有点类似include，但区别在于，我们可以在callback中返回一个数组，
 * 这个数组将作为被包含模板的context。
 */
class Customtags extends Shine_Tag_Abstract {

    public function getLibrary() {
        return array(
            'js' => array(
                'type' => 'simpletag',
                'callback' => array(__CLASS__, 'js')
            ),

            'css' => array(
                'type' => 'simpletag',
                'callback' => array(__CLASS__, 'css')
            ),

            'asset' => array(
                'type' => 'simpletag',
                'callback' => array(__CLASS__, 'asset')
            ),

			'link_to' => array(
				'type' => 'simpletag',
				'callback' => array(__CLASS__, 'link_to')
			),
			
			'link_to_img' => array(
				'type' => 'simpletag',
				'callback' => array(__CLASS__, 'link_to_img')
			),
			
			'image_tag' => array(
				'type' => 'simpletag',
				'callback' => array(__CLASS__, 'image_tag')
			),
			
            'widget' => array(
                'type' => 'inclusiontag',
                'callback' => array(__CLASS__, 'widget'),
                'template' => 'show.html'
            ),
			'jquery_validation' => array(
				'type' => 'simpletag',
				'callback' => array(__CLASS__, 'jquery_validation'),
			),
        );
    }

	public static function jquery_validation()
	{
        $ajaxHandler = <<<EOC
    <script type="text/javascript">
    window.ajaxHandler = function(f) {
        $(f).ajaxSubmit({
            dataType: "json",
            success: function(result, status, xhr, f) {
                if (result.type == 'success') {
                    if (result.path) {
                       window.location.href = '/cpanel/' + result.path;
                    } else if (result.location) {
                        window.location.href = result.location;
                    }
                } else if (result.type == 'error') {
                    alert(result.data);
                }
            }
        });
    return false;
    }
    </script>

EOC;
		return self::js("js/jquery.validate_pack.js") . self::js('js/jquery.form.js') . $ajaxHandler;
	}

    public static function js($uri) {
        return '<script type="text/javascript" src="' . self::asset($uri) .'"></script>';
    }

    public static function css($uri) {
        return '<link rel="stylesheet" href="' . self::asset($uri) .'" type="text/css" />';
    }

    public static function asset($uri) {
        return 'http://static.xindianbao.com/' . $uri;
    }


	public static function link_to($content, $link)
	{
		return '<a href="' . $link .'">'. $content .'</a>';
	}
	
	public static function link_to_img($uri, $link)
	{
		return '<a href="' . $link .'">'. self::image_tag($uri) .'</a>';
	}
	
	public static function image_tag($uri)
	{
		return '<img src="' . self::asset($uri) .'" />';
	}

    public static function widget() {
        return array('widget_name' => 'hello');
    }
}

?>
