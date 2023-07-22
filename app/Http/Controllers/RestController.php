<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ParseException;
use L5Swagger\ConfigFactory;

/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Guilherme Ramos Correia <guilhermecorreiaramos@gmail.com>
 */
class RestController extends Controller
{
    protected function all( Model $model, $where=null, $order = null ){
        try {

            if($where <> null)
                $all = $model->where($where['field'], $where['value'])->orderBy('id', 'ASC')->get();
            else if($order <> null)
                $all = $model->orderBy($order, 'ASC')->get();
            else
                $all = $model->orderBy('id', 'ASC')->get();

            if ($all->count())
                return $this->encodeJsonResponse(true, $all->toArray());
            else
                return $this->encodeJsonResponse(true, [], 200);
        }
        catch( \Exception $e ){
            return $this->encodeError( $e->getMessage(), $e->getCode() );
        }
    }
    /**
     * @param Model $model
     * @param $id
     * @return JsonResponse
     */
    protected function find( Model $model, $id ){
        try {
            /**
             * @var Model $obj
             */
            if(is_array($id))
                $obj = $model->where($id)->first();
            else
                $obj = $model->find($id);

            if($obj != null)
                return $this->encodeJsonResponse(true, $obj->toArray());
            else
                return $this->encodeJsonResponse(true, null, 200);
        }
        catch( \Exception $e ){
            return $this->encodeError( $e->getMessage(), $e->getCode() );
        }
    }

    /**
     * @param Model $model
     * @param $params
     * @return JsonResponse
     */
    protected function search( Model $model, $params ){
        try {

            $where = $this->makeWheres($params);

            if(count($where) > 0)
                $result = $model->where($where)->paginate(env('DB_PAGESIZE',20));
            else
                $result = $model->paginate(env('DB_PAGESIZE', 20));

            if($result->total() > 0)
                return $this->encodePagination($result);
            else
                return $this->encodeJsonResponse(false, null, 404);

        }
        catch( \Exception $e ){
            return $this->encodeError( $e->getMessage(), $e->getCode() );
        }
    }

    /**
     * @param Model $model
     * @param Request $request
     * @return JsonResponse
     */
    protected function create( Model $model, Request $request ){
        try {
            /**
             * @var Model $obj
             */
            $obj = $model->create($request->all());
            $obj = $model->find($obj->id);
            return $this->encodeJsonResponse(true,$obj->toArray());
        }
        catch( \Exception $e ){
            return $this->encodeError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Model $model
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    protected function edit( Model $model, Request $request, $id ){
        try {
            /**
             * @var Model $obj
             */
            if(is_array($id))
                $obj    = $model->where($id)->first();
            else
                $obj    = $model->find($id);

            if($obj != null) {
                $obj->update($request->all());

                if(is_array($id))
                    $obj    = $model->where($id)->first();
                else
                    $obj    = $model->find($id);
            }
            else
                return $this->encodeJsonResponse(false,null,404);

            return $this->encodeJsonResponse(true, $obj->toArray());
        }
        catch( \Exception $e ){
            return $this->encodeError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param Model $model
     * @param $id
     * @return JsonResponse
     */
    protected function remove( Model $model, $id ){
        try {

            /**
             * @var Model $obj
             */
            if(is_array($id))
                $obj    = $model->where($id)->first();
            else
                $obj    = $model->find($id);

            if($obj != null)
                $obj->delete();
            else
                return $this->encodeJsonResponse(false,null, 404);

            return $this->encodeJsonResponse(true,null);
        }
        catch( \Exception $e ){
            return $this->encodeError($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param $result
     * @param array $embeded
     * @param int $httpcode
     * @return JsonResponse
     */
    protected function encodeJsonResponse( $result ,$data=null, $httpcode=200 ){
        return new JsonResponse( array( 'return'=>$result, 'embedded'=>$data ), $httpcode );

    }
    /**
     * @param $result
     * @param array $embeded
     * @param int $httpcode
     * @return JsonResponse
     */
    protected function encodeJsonResponseRel($result, $data=null, $saldo=0, $receita=0, $despesa=0, $httpcode=200 ){
        return new JsonResponse(
            array(
                'return' => $result,
                'embedded' => $data,
                'receita' => $receita,
                'despesa' => $despesa,
                'saldo' => $saldo
            ),
            $httpcode
        );
    }

    /**
     * @param $result
     * @return JsonResponse
     */
    protected function encodePagination( $result, $request ){

        /**
         * instancia array
         */

        $json = array();
        $pageSize = env('DB_PAGESIZE', 25);
        $pageNumber = $request->page ?? 1; // Recupera a segunda página
        /**
         * @var Model $r
         */
        $result = $result->paginate($pageSize, ['*'], 'page', $pageNumber);

        foreach( $result as $r ){
            // adiciona todos elementos no array em formato de array
            $json[] = $r->toArray();
        }

        return new JsonResponse(array(
            'return'=>true,
            'embedded'=>$json,
            'next'=>$result->hasMorePages() ? ($result->currentPage() + 1) : null,
            'back'=>($result->currentPage() > 1) ? ($result->currentPage() - 1) : null ,
            'pageCount'=>$result->lastPage(),
            'pageSize'=>$pageSize,
            'page' => $result->currentPage(),
            'total'=>$result->total()
        ));
    }

    /**
     * @param $message
     * @param $code
     * @return JsonResponse
     */
    protected function encodeError( $message, $code, $httpErrorCode = 500 ){
        return new JsonResponse( array( 'return'=>false, 'message'=>$message, 'code'=>$code ), $httpErrorCode);
    }

    protected function makeWheres( Array $params ){

        $result = [];

        /**
         * Exemplo de uso
         * $param['nome_do_campo'] = array('value'=>'%termo para consulta%', 'operador'=>'like');
         * $param['id'] = array('value'=>'valor', 'operador'=>'=');
         */
        foreach( $params as $p ) {
            if ($p['value'] <> '' && $p['value'] <> 'null' && $p['value'] <> "%null%" && $p['value'] <> "(null)") {
                if ($p['operador'] == 'like' || $p['operador'] == '=' || $p['operador'] == '<>') {
                    $result[] = [key($params), $p['operador'], $p['value']];
                } else if ($p['operador'] == 'between') {
                    /**
                     * Para passagem de periodos o parametro deve ser passado com duas data separadas pelo delimitador "|"
                     */
                    $values = explode('|', $p['value']);
                    $result[] = [key($params), '>=', $values[0]];
                    $result[] = [key($params), '<=', $values[1]];

                }
            }
            next($params);
        }

        return $result;
    }
    /**
     *@param $request
     *@return JsonResponse
     */
    protected function authentication(Request $request, $endPoint){
        $http = new Client();
            $response = $http->post($endPoint,[
                'form_params'=>[
                    'grant_type' => 'authorization_code',
                    'usuario' => 'fdsfsf',
                    'password' => 'dsf',
                    //'code' => $request->code
                ]
            ]);
            $request->session()->put('token',json_decode((string) $response->getBody(), true)['token']);
    }

    /**
     * metódo GET
    */
    protected function GET($endPoint){
        $http = new Client();
        $response = $http->get($endPoint,[
            'headers' => [
                'Accept' => 'application/json'
        ],
        ]);

        $teste = $response->getBody();
        $content =$teste->getContents();
        //return $content;
        return json_decode($content,true);
    }

    /**
     * função responsável por verificar se o Download foi realizado
     */
    protected function verifyDownloadVersao($vPastaVersao){
        if(!\file_exists($vPastaVersao.'end.ini') AND !\file_exists($vPastaVersao.'versao.zip')){
            $datetime =(new \DateTime())->format('Y-m-d h:i:s');
            $texto = 'Baixado'." ".'DATA'." ".$datetime;
            $file = \fopen($vPastaVersao.'/end.ini','w');
            \fwrite($file,$texto);
            fclose($file);
            return true;
        }
        return false;
    }
    /**
     * função responsável para realizar o dowload do arquivo do para o atualizador
     */
    protected function downloadFile(Request $request,$endPoint, $vPastaVersao){
        $path = $vPastaVersao;
        $file_path = \fopen($path,'w');
        $http = new Client();
        $response = $http->get( $endPoint,['save_to' =>$file_path],[
            'headers' => [
                'Accept' => 'application/json',
                'x-api-key' => 'p99ZBPnDKYzt9KmJHB8YoVYDg9Pu6FE74T86',
            ],
        ]);
    }


    protected function readPDF($request){
        $filename = 'C:/pdf/'.$request->lot;
        if (!file_exists($filename)) {
            mkdir($filename,777);
        }
        $decoded = base64_decode($request->data);
        $file = $filename.'/'.$request->medicalRecord.'.pdf';
        file_put_contents($file, $decoded);
        //return $file;
        if (file_exists($file)) {
            header("Content-Description: File Transfer");
            header("Content-Type: application/pdf");
            header("Content-Disposition: attachment; filename=".$file);
            header("Content-Transfer-Encoding: binary");
            header("Expires: 0");
            header("Cache-Control: must-revalidate");
            header("Pragma: public");
            header("Content-Length: " . strlen($file));
            ob_clean();
            flush();
            echo $file;
            //$this->encodeJsonResponse(true,'inserido com sucesso',200);
            exit;
        }
    }
    protected function validar_cpf($cpf) {

        // Extrai somente os números
        $cpf = preg_replace( '/[^0-9]/is', '', $cpf );

        // Verifica se foi informado todos os digitos corretamente
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Faz o calculo para validar o CPF
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
        return true;

    }
    protected function validar_cnpj($cnpj)
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $cnpj);

        // Valida tamanho
        if (strlen($cnpj) != 14)
            return false;

        // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;

        // Valida primeiro dígito verificador
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto))
            return false;

        // Valida segundo dígito verificador
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++)
        {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }

        $resto = $soma % 11;

        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }

}
