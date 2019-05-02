<?php

namespace App\Controllers; //local onde está inserido
use App\Indexes;
use GuzzleHttp\Client;

class IndexesController
{

    /*
     * Método: indexes() 
     * Pârametros: $data ($_POST or NULL)
     * Retorno: json / false
     * 
     * Objetivo: Verificar se recebeu algum $_POST para mostrar o index respetivo ao index que vai no array;
     *           Caso contrário, verificar se a tabela 'indexes' tem dados. Se não tiver, chama o método 
     *           getServiceData()
     *
    */
    public function indexes($data = NULL, $hash = NULL) 
    {
        if ($hash) {

            // if it has a $_POST it means we're trying to show a specific index (POST has the id)
            if($data) {

                $id = $data['index_id'];      
                $index = json_decode(Indexes::find_by_id($id));   

                LogsController::register_log('Picked ' . $index->symbol . '.');

                header('content-type: application/json');
                //return json_encode($index); // retornar para a chamada AJAX 
                return $index; // já está encoded         

            } else {

                $indexes = json_decode(Indexes::get_indexes()); 

                // antes do show() verificar se a tabela indexes tem dados
                if ($indexes) { 
                    LogsController::register_log('Accessed indexes page.');

                    //$this->show_indexes($indexes);
                    header('content-type: application/json');
                    //return json_encode($indexes); 
                    return $indexes; // já está encoded


                } else {
                    $data = json_decode($this->getServiceData());

                    $this->saveData($data); //populate the data into table indexes

                    header("Location: indexes"); //mandar de volta para a página de indexes                
                    
                }

            }

        }
        return false;
    }

    /*
     * Método: getServiceData() 
     * Pârametros: -
     * Retorno: $indexes (vazio ou com indexes)
     * 
     * Objetivo: Popular o array $indexes com a informação pretendida, através do decode da info na API
     *
    */
    public function getServiceData()
    {

        $indices = 'https://www.xtb.com/api/uk/instruments/get?queryString=&branchName=uk&instrumentTypeSlug=indices&page=1&_=1550592039763';

        $client = new Client();

        $res = $client->request('GET', $indices);

        // Decode JSON string to PHP variable
        $result = json_decode((string)$res->getBody()); // true to turn it into an assoc array

        /*

        Data Structure:

        instrumentsCollectionLimited: {
            indices: {
                AUS200: {
                    symbol: "AUS200",
                    
                    type: "indices",
                    
                    trading_hours: "12:05 am - 6:30 am and 7:15 am - 09:00 pm CET; 2:05 am - 8:30 am and 9:15 am - 23:00 pm CEST""""",
                    
                    description: "Instrument which price is based on quotations of the contract for index reflecting 200 largest Australian stocks quoted on the Australian regulated market.",
                    
                    spread_target_standard: "5",
        ...            

        */

        //var_dump($result);

        $indexes = [];
        $requirements = ['symbol', 'type', 'trading_hours', 'description', 'spread_target_standard'];

        foreach($result->instrumentsCollectionLimited->indices as $index => $properties) {

            foreach($properties as $property => $value) {

                for ($i=0; $i < count($requirements); $i++) {

                    if($property === $requirements[$i]) {
                        $indexes[$index][] = $value;  
                    }

                }
                         
            } 

        }

        /*
        echo "<pre>";
        var_dump($indexes);
        echo "</pre>";
        */

        return json_encode($indexes);

    }    

    /*
     * Método: saveData() 
     * Pârametros: $data (vazio ou indexes)
     * Retorno: true / false (depende se tem indexes)
     * 
     * Objetivo: Popular o array $indexes com a informação pretendida, através do decode da info na API
     *
    */
    public function saveData($data)
    {
        foreach($data as $index => $properties) {
            //var_dump($properties);
            $ind = new Indexes;
            $ind->set_properties($properties);
            $ind->save();
            //Indexes::insert_index($properties);
        }

        return $ind->get_indexes() ? true : false;
    }

    /*
    public function show_indexes($data)
    {
        View::render('header');
        View::render('indexes', $data);
        View::render('footer');
    }
    */
}

?>