<?php

class Dashboard{

    public $data_inicio;
    public $data_fim;
    public $numero_de_vendas;
    public $total_de_vendas;
    public $clientes_ativos;
    public $clientes_inativos;
    public $total_de_reclamacoes;
    public $total_de_elogios;
    public $total_de_sugestoes;
    public $total_de_despesas;

    public function __get($atributo)
    {
        return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
        return $this;
    }
}
/*
"<br />
<b>Fatal error</b>:  Uncaught PDOException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'total' in 'field list' in C:\xampp\htdocs\bkp\arquivos_ajax_jquery\app_dashboard\app.php:85
Stack trace:
#0 C:\xampp\htdocs\bkp\arquivos_ajax_jquery\app_dashboard\app.php(85): PDOStatement-&gt;execute()
#1 C:\xampp\htdocs\bkp\arquivos_ajax_jquery\app_dashboard\app.php(129): BD-&gt;get_total_de_vendas()
#2 {main}
  thrown in <b>C:\xampp\htdocs\bkp\arquivos_ajax_jquery\app_dashboard\app.php</b> on line <b>85</b><br />
"
*/
class Conexao{
    private $host = '127.0.0.1:3307';
    private $dbname = 'matheus';
    private $user ="root" ;
    private $pass = '' ;

    public function conectar(){
        
        try{

            $conexao = new PDO(
                "mysql:host=$this->host;dbname=$this->dbname",
                "$this->user",
                "$this->pass"
            );
            $conexao->exec('set charset utf8');

           return $conexao;
           
        }
        catch(PDOException $e){
            echo '<p>' .$e->getMessage() .'</p>';
        }
    }
}

class BD{
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao,Dashboard $dashboard)
    {
        $this->conexao = $conexao->conectar();
        $this->dashboard = $dashboard;
    }

    public function get_numero_de_vendas(){
        $query= 'select count(*) as numero_vendas from tb_vendas where data_venda between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio',$this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }

    public function get_total_de_vendas(){
        $query= 'select SUM(valor_da_venda) as total_vendas from tb_vendas  where data_venda between :data_inicio and :data_fim';

        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(':data_inicio',$this->dashboard->__get('data_inicio'));
        $stmt->bindValue(':data_fim',$this->dashboard->__get('data_fim'));
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }

    public function get_clientes_ativos(){
        $query= 'select count(*) as total_ativos from tb_clientes  where cliente_ativo = 1';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_ativos;
    }
    public function get_clientes_inativos(){
        $query= 'select count(*) as total_inativos from tb_clientes  where cliente_ativo = 0';

        $stmt = $this->conexao->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_OBJ)->total_inativos;
    }


}

$dashboard = new Dashboard();
$conexao = new Conexao();

$competencia = explode('-',$_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];

$dias_do_mes = cal_days_in_month(CAL_GREGORIAN,$mes,$ano);


$dashboard->__set('data_inicio',$ano. '/' .$mes.'/' .'-01');
$dashboard->__set('data_fim',$ano. '/' .$mes.'/' .$dias_do_mes);

$bd = new BD($conexao,$dashboard);



$dashboard->__set('numero_de_vendas',$bd->get_numero_de_vendas());
//print_r($bd->get_numero_de_vendas());
$dashboard->__set('total_de_vendas',$bd->get_total_de_vendas());
//print_r($bd->get_total_de_vendas());
$dashboard->__set('clientes_ativos',$bd->get_clientes_ativos());
$dashboard->__set('clientes_inativos',$bd->get_clientes_inativos());
echo json_encode($dashboard);
?>