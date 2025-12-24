<?php



namespace App\Http\Middleware;



use Closure;

use Illuminate\Http\Request;

use Symfony\Component\HttpFoundation\Response;



class ReplaceLegacyStorageDomain

{

    /**

     * Handle an incoming request.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next

     * @return \Symfony\Component\HttpFoundation\Response

     */

    public function handle(Request $request, Closure $next): Response

    {

        /** @var \Symfony\Component\HttpFoundation\Response $response */

        $response = $next($request);



        // Sólo procesar respuestas de texto/HTML/JSON, no archivos binarios

        $contentType = $response->headers->get('Content-Type');



        if ($contentType && preg_match('/(text|json|javascript|xml)/i', $contentType)) {

            $content = $response->getContent();



            if (is_string($content) && str_contains($content, 'https://sivyc.icatech.gob.mx/storage')) {

                $content = str_replace(

                    'https://sivyc.icatech.gob.mx/storage',

                    'https://archivos.icatech.com.mx',

                    $content

                );



                $response->setContent($content);

            }

        }



        return $response;

    }

}

