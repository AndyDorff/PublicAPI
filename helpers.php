<?php
namespace Modules\PublicAPI;

use Modules\PublicAPI\Factories\Response\ResponseFactory;

if(!function_exists(__NAMESPACE__.'\base64_rand')){
    function base64_rand($length, string $seed = null): string
    {
        if($seed){
            mt_srand(crc32($seed));
        }

        return substr(base64_encode(md5(mt_rand())), 0, $length);
    }
}