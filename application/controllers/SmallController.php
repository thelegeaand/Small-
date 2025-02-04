<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SmallController extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
        $this->load->helper('form');
        $this->load->database();
        $this->load->library('email');
        $this->load->library("session");
        $this->load->library('form_validation');
        $this->load->model('SmallModel');
        $this->load->library('cart');
        $this->CI = & get_instance();
    }

    public function index()
    {

        $this->load->view('Home');
    }

    public function TramitarComandaRed()
    {

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');
        } else {


            if ($session == "client") {

                $this->load->view('TramitarComanda');
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function HistorialComandes()
    {
        $session = $this->session->userdata('tipus');
        $idusuari= $this->session->userdata('validat');

        if (empty($session)) {
            $this->load->view('Home');
        } else {
            if ($session == "client") {

                $Client = $this->SmallModel->IdClient($idusuari);
                $idclient = $Client[0]['id_client'];

                $dades=$this->SmallModel->DadesComandesUsuari($idclient);

                $this->load->view('Comanda',array('comandes'=>$dades));

            } else {

                $this->load->view('Home');
            }
        }
    }

    public function CancelEntrega($idcomanda){
        $session = $this->session->userdata('tipus');
       

        if (empty($session)) {
            $this->load->view('Home');
        } else {
            if ($session == "client") {

             
                $this->SmallModel->Cancel($idcomanda);

                $this->load->view('ComandaCancel·lada');

            } else {

                $this->load->view('Home');
            }
        }

    }

public function ModificarApariencia(){

        $session = $this->session->userdata('tipus');
        $idusuari = $this->session->userdata('validat');
        $botiga= $this->SmallModel->IdBotiga($idusuari);
        $idbotiga=$botiga[0]["id_botiga"];

        if (empty($session)) {

            $this->load->view('Home');
        } else {

            if ($session == "botiga") {

                $data = $this->SmallModel->Botiga($idbotiga);
                       
                $this->load->view('ModDadesBotiga',array('Abotiga'=>$data));
  
            } else {

                $this->load->view('Home');
            }
        }



    }

    public function ModDadesPersonaRed()
    {

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');
        } else {

            if ($session == "client") {

                $this->load->view('ModDadesPersonals');

                
            } else {

                $this->load->view('Home');
            }
        }
     
    }
    public function registreclient()
    {

        $Nom = $this->input->post('Nom');
        $PrimerCognom = $this->input->post('Cognom');
        $SegonCognom = $this->input->post('Cognom2');
        $Dni = $this->input->post('Dni');
        $DataNaixement = $this->input->post('datanaixement');
        $Correu = $this->input->post('correu');
        $Ciutat = $this->input->post('ciutat');
        $Provincia = $this->input->post('provincia');
        $CodiPostal = $this->input->post('cpostal');
        $Password = $this->input->post('password');
        $NomUsuari = $this->input->post('NomUsuari');
        $passwordE = md5($Password);
        $data['dada'] = $this->input->post();

        $this->form_validation->set_rules('correu', 'correu', 'is_unique[client.correu]');

        if ($this->form_validation->run() == FALSE) {

            echo "notunique";
        } else {

            $this->SmallModel->NouUsuari($NomUsuari, $passwordE, 1);

            $dades = $this->SmallModel->CodiUsuari($NomUsuari);

            $id = $dades[0]['id_usuari'];

            $this->SmallModel->NouClient($id, $Nom, $PrimerCognom, $SegonCognom, $Dni, $DataNaixement, $Correu, $Ciutat, $CodiPostal, $Provincia);

            echo "ok";
        }
    }
    public function registrebotigues()
    {
        $Numero = $this->input->post('Number');
        $NomPropietari = $this->input->post('NomPropietari');
        $NomUsuari = $this->input->post('NomUsuari');
        $Password = $this->input->post('Password');
        $NomBotiga = $this->input->post('NomBotiga');
        $NomEmpresa = $this->input->post('NomEmpresa');
        $TipusBotiga = $this->input->post('TipusBotiga');
        $CIF = $this->input->post('Cif');
        $CorreuEmpresa = $this->input->post('CorreuE');
        $Ciutat = $this->input->post('Ciutat');
        $Carrer = $this->input->post('Carrer');
        $Provincia = $this->input->post('Provincia');
        $CodiPostal = $this->input->post('cpostal');
        
        $Iban = $this->input->post('IbanComplet');
        $passwordE = md5($Password);
        $data['dada'] = $this->input->post();

        $this->form_validation->set_rules('CorreuE', 'CorreuE', 'is_unique[botiga.correu]');
        $this->form_validation->set_rules('NomUsuari', 'NomUsuari', 'is_unique[usuaris.nom_usuari]');

        if ($this->form_validation->run() == FALSE) {

             echo "notunique";

        } else {

            $this->SmallModel->NouUsuari($NomUsuari, $passwordE, 0);

            $dades = $this->SmallModel->CodiUsuari($NomUsuari);

            $id = $dades[0]['id_usuari'];

            $this->SmallModel->NovaBotiga($id, $NomPropietari,$NomBotiga, $TipusBotiga,$NomEmpresa, $CIF, $CorreuEmpresa, $Ciutat, $Carrer,$Provincia,$CodiPostal,$Iban,$Numero);
            
            echo "ok";

            
        }
    }

    public function inicisessio()
    {
        $NomUsuari = $this->input->post('NomUsuariIS');
        $Password = $this->input->post('PasswordIS');
        $comp = "";
        $PasswordE = md5($Password);

        $cont = $this->SmallModel->InciarSessio($NomUsuari, $PasswordE);

        if ($cont != 0) {
            $comp = "ok";

            $dades = $this->SmallModel->CodiUsuari($NomUsuari);

            $idusuari = $dades[0]['id_usuari'];

            $contClientsId = $this->SmallModel->compClient($idusuari);
            $contAdminId = $this->SmallModel->compAdmin($idusuari);
            $contBotigaId = $this->SmallModel->compBotiga($idusuari);
            $contRepartidorId = $this->SmallModel->compRepartidor($idusuari);
            $contAtencioId = $this->SmallModel->compAtencio($idusuari);

            if ($contClientsId != 0) {
                $comp = "client";
                $this->load->library("session");
                $this->session->set_userdata("validat", $idusuari);
                $this->session->set_userdata("tipus", $comp);
                echo $comp;
            } else if ($contAdminId != 0) {

                $comp = "admin";
                $this->load->library("session");
                $this->session->set_userdata("validat", $idusuari);
                $this->session->set_userdata("tipus", $comp);
                echo $comp;
            } else if ($contBotigaId != 0) {

                $comp = "botiga";
                $this->load->library("session");
                $this->session->set_userdata("validat", $idusuari);
                $this->session->set_userdata("tipus", $comp);
                echo $comp;
            } else if ($contRepartidorId != 0) {

                $comp = "repartidor";
                $this->load->library("session");
                $this->session->set_userdata("validat", $idusuari);
                $this->session->set_userdata("tipus", $comp);
                echo $comp;
            } else if ($contAtencioId) {

                $comp = "atencioclient";
                $this->load->library("session");
                $this->session->set_userdata("validat", $idusuari);
                $this->session->set_userdata("tipus", $comp);
                echo $comp;
            }
        } else {
            $comp = "no";
            echo $comp;
        }
    }

    public function ModificarABotiga(){

        $session = $this->session->userdata('tipus');
        $idusuari = $this->session->userdata('validat');
        $botiga= $this->SmallModel->IdBotiga($idusuari);
        $idbotiga=$botiga[0]["id_botiga"];

        
        if (empty($session)) {

            $this->load->view('Home');
        } else {

            if ($session == "botiga") {

                $nombotiga=$this->input->post('nombotiga');
                $desc=$this->input->post('descripcio');
                $contacte=$this->input->post('contacte');
                $data['dades']=$this->input->post();
                $this->form_validation->set_rules('nombotiga','nombotiga','required|max_length[50]',
                array('required'=>'<span id="error">*Obligatori</span>'
                ,'max_length'=>' <span id="error">*Max.10 caràcters</span>'
                ));
                $this->form_validation->set_rules('descripcio','descripcio','required|max_length[200]',
                array('required'=>'<span id="error">*Obligatori</span>'
                ,'max_length'=>' <span id="error">*Max.200 caràcters</span>'
                ));
                $this->form_validation->set_rules('contacte','contacte','required|max_length[50]',
                array('required'=>'<span id="error">*Obligatori</span>'
                ,'max_length'=>' <span id="error">*Max.200 caràcters</span>'
                ));

                if($this->form_validation->run()==FALSE){
        
                    $missatge="Algun camp incorrecte!!!";
                    $this->load->view('ErrorCanvis');

                }else{

                    $this->SmallModel->ModificarBotiga($idbotiga,$nombotiga,$contacte,$desc);

                    $this->load->view('CanvisCorrectes');

                }  
              
                
            } else {

                $this->load->view('Home');
            }
        }
    }
        public function ModificarLogo(){

            $session = $this->session->userdata('tipus');
            $idusuari = $this->session->userdata('validat');
            $botiga= $this->SmallModel->IdBotiga($idusuari);
            $idbotiga=$botiga[0]["id_botiga"];
    
            
            if (empty($session)) {
    
                $this->load->view('Home');
            } else {
    
                if ($session == "botiga") {
    
                    $config['upload_path']          = './uploads/';
                    $config['allowed_types']        = 'jpg|png';
                    $config['max_size']             = 1000;
                    $config['max_width']            = 1920;
                    $config['max_height']           = 1080;
    
                    $this->load->library('upload', $config);
    
                    if ( ! $this->upload->do_upload('perfil'))
                    {
                            $error = array('error' => $this->upload->display_errors());
    
                            $this->load->view('ErrorCanvis', $error);
                    }
                    else
                    {
                        $data = array('upload_data' => $this->upload->data());
                    
                        foreach($data as $key){
                            
                            echo "<script>console.log('Debug Objects:aqui " . $key['file_name'] . "' );</script>";
                             echo "<script>console.log('Debug Objects:aqui " . $key['file_type'] . "' );</script>";
                           
                           
                            
                            $nomF=$key['file_name'];
                            $tipusF=$key['file_type'];
                            
                        
                            
                        }
                        
                        
                         $CodiU=$this->session->userdata('validat');
                         $this->SmallModel->PujarnouLogo($idbotiga,$nomF,$tipusF);
                         $this->load->view('CanvisCorrectes');
                        
                    }
                  
                    
                } else {
    
                    $this->load->view('Home');
                }
            }
        }

            public function ModificarBanner(){

                $session = $this->session->userdata('tipus');
                $idusuari = $this->session->userdata('validat');
                $botiga= $this->SmallModel->IdBotiga($idusuari);
                $idbotiga=$botiga[0]["id_botiga"];
        
                
                if (empty($session)) {
        
                    $this->load->view('Home');
                } else {
        
                    if ($session == "botiga") {
        
                        $config['upload_path']          = './uploads/';
                        $config['allowed_types']        = 'jpg|png';
                        $config['max_size']             = 1000;
                        $config['max_width']            = 1920;
                        $config['max_height']           = 1080;
        
                        $this->load->library('upload', $config);
        
                        if ( ! $this->upload->do_upload('banner'))
                        {
                                $error = array('error' => $this->upload->display_errors());
        
                                $this->load->view('ErrorCanvis', $error);
                        }
                        else
                        {
                            $data = array('upload_data' => $this->upload->data());
                        
                            foreach($data as $key){
                                
                                echo "<script>console.log('Debug Objects:aqui " . $key['file_name'] . "' );</script>";
                                 echo "<script>console.log('Debug Objects:aqui " . $key['file_type'] . "' );</script>";
                               
                               
                                
                                $nomF=$key['file_name'];
                                $tipusF=$key['file_type'];
                                
                            
                                
                            }
                            
                             $CodiU=$this->session->userdata('validat');
                             $this->SmallModel->PujarnouBanner($idbotiga,$nomF,$tipusF);
                             $this->load->view('CanvisCorrectes');
                            
                        }
                      
                        
        } else {
        
                    $this->load->view('Home');
        }
        }
    







     




  
    }

    public function TipusBotiga(){

        $tipus = $this->input->post('select');

        if($tipus==""||empty($tipus)){

            $tipus="Carn";
        }
    
        if($tipus=="Carn"){

            $titol="LA CARN";
            $dades=$this->SmallModel->MostrarBotiguesPer("Carn");
            $this->load->view('MainBotiguesPersona',array('dades'=>$dades,'titol'=>$titol));

        }else if($tipus=="Peix"){

            $titol="EL PEIX";
            $dades=$this->SmallModel->MostrarBotiguesPer("Peix");
            $this->load->view('MainBotiguesPersona',array('dades'=>$dades,'titol'=>$titol));


        }else if($tipus=="Aviram"){
            
            $titol="AVIRAM";
            $dades=$this->SmallModel->MostrarBotiguesPer("Aviram");
            $this->load->view('MainBotiguesPersona',array('dades'=>$dades,'titol'=>$titol));


        }else if($tipus=="Fruita"){

            $titol="LA FRUITA";
            $dades=$this->SmallModel->MostrarBotiguesPer("Fruita");
            $this->load->view('MainBotiguesPersona',array('dades'=>$dades,'titol'=>$titol));

        }else{

            $this->load->view('Home');
        }
        
    }
    public function Contacte()
    {
        $this->load->view('Contacte');
    }


    public function MainBotiguesPer()
    {

        $this->load->view('MainBotiguesPersona',);
    }

    public function RegistreBotiga()
    {

        $this->load->view('RegistreBotigues');
    }

    public function IniciClient()
    {

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');
        } else {

            if ($session == "client") {

                $sessionid = $this->session->userdata('validat');

                $dades = $this->SmallModel->CiutatClient($sessionid);

                $ciutat = $dades[0]['ciutat'];
                $ciutatMajus = mb_strtoupper($ciutat);
                $tipus="Carn";
                $botiga="CARNISSERIES/XARCUTERIES";

                $data = $this->SmallModel->MostrarBotiguesCiutat($tipus,$ciutat);

                $this->load->view('IniciClient',array('ciutat'=>$ciutatMajus,'botiga'=>$botiga,'dades'=>$data));
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function IniciClient2()
    {
        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');
        } else {

            if ($session == "client") {

                $sessionid = $this->session->userdata('validat');

                $dades = $this->SmallModel->CiutatClient($sessionid);

                $ciutat = $dades[0]['ciutat'];
                $ciutatMajus = mb_strtoupper($ciutat);
                $tipus="Aviram";
                $botiga="L'AVIRAM";

                $data = $this->SmallModel->MostrarBotiguesCiutat($tipus,$ciutat);

                $this->load->view('IniciClient2',array('ciutat'=>$ciutatMajus,'botiga'=>$botiga,'dades'=>$data));
            } else {

                $this->load->view('Home');
            }
        }
    }
    public function IniciClient3()
    {
        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');
        } else {

            if ($session == "client") {

             $sessionid = $this->session->userdata('validat');

                $dades = $this->SmallModel->CiutatClient($sessionid);

                $ciutat = $dades[0]['ciutat'];
                $ciutatMajus = mb_strtoupper($ciutat);
                $tipus="Peix";
                $botiga="PEIXATERIES";

                $data = $this->SmallModel->MostrarBotiguesCiutat($tipus,$ciutat);
                
                $this->load->view('IniciClient3',array('ciutat'=>$ciutatMajus,'botiga'=>$botiga,'dades'=>$data));
            } else {

                $this->load->view('Home');
            }
        }
    }
    public function IniciClient4()
    {
        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {

                $sessionid = $this->session->userdata('validat');

                $dades = $this->SmallModel->CiutatClient($sessionid);

                $ciutat = $dades[0]['ciutat'];
                $ciutatMajus = mb_strtoupper($ciutat);
                $tipus="Fruita";
                $botiga="FRUITERIES";

                $data = $this->SmallModel->MostrarBotiguesCiutat($tipus,$ciutat);
                
                $this->load->view('IniciClient4',array('ciutat'=>$ciutatMajus,'botiga'=>$botiga,'dades'=>$data));

            } else {

                $this->load->view('Home');
            }
        }
    }

    public function CompteClient()
    {

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');
        } else {

            if ($session == "client") {

                $this->load->view('ModDadesPersonals');
            } else {

                $this->load->view('Home');
            }
        }
    }


    public function IniciBotiga()
    {
        $session = $this->session->userdata('tipus');
        $idusuari = $this->session->userdata('validat');
        $botiga= $this->SmallModel->IdBotiga($idusuari);
        $idbotiga=$botiga[0]["id_botiga"];

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "botiga") {

                $data = $this->SmallModel->Botiga($idbotiga);
                $data2 = $this->SmallModel->Productes($idbotiga);
                
                $this->load->view('IniciBotiga',array('Abotiga'=>$data,'Pbotiga'=>$data2));

          

            } else {

            $this->load->view('Home');
            }
        }
    }

    
/**
* Afegir estoc  producte a la botiga.
*
* @param int $idproducte id del producte afegir.
*/

    public function AfegirProducte(){

        $session = $this->session->userdata('tipus');
        $idusuari = $this->session->userdata('validat');
        $idproducte=$NomUsuari = $this->input->post('idproducte');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "botiga") {

                

                $pro=$this->SmallModel->CompEstoc($idproducte);
                $EstocActual = $pro[0]['estoc'];
                $this->SmallModel->AfegirEstocProducte($EstocActual,$idproducte);
               
                echo"ok";

            } else {

            $this->load->view('Home');
            }
        }

    }
    public function Botiga($idbotiga){

        $session = $this->session->userdata('tipus');


        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {

                $data = $this->SmallModel->Botiga($idbotiga);
                $data2 = $this->SmallModel->Productes($idbotiga);
                
                $this->load->view('Botiga',array('Abotiga'=>$data,'Pbotiga'=>$data2));

            } else {

                $this->load->view('Home');
            }
        }

    }

    public function AfegirCarrito(){

        $session = $this->session->userdata('tipus');
        $id = $this->input->post('id');
        $quantitat = $this->input->post('quantitat');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {
                $ProdInfo = $this->SmallModel->ControlProductes($id);
                $preu = $ProdInfo[0]['preu_kg'];
                $img= $ProdInfo[0]['img_prod'];
                $tipus=$ProdInfo[0]['tipus_prod'];
                $nom=$ProdInfo[0]['nom'];

                    $data = array(
                        'id'      => $id,
                        'qty'     => $quantitat,
                        'price'   => $preu,
                        'name'    => $nom,
                        'options' => array('img' => $img, 'tipus' => $tipus)
                );
                
            
                 $this->cart->insert($data);

                

                echo"ok";

            } else {

                $this->load->view('Home');
            }
        }  
    }

    public function BuidarCarrito(){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {

                $this->cart->destroy();
                $this->load->view('BuidarCarrito');
    
            } else {

                $this->load->view('Home');
            }
        }

      
        
    }

    public function TramitarCarrito(){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {


                if(empty($this->cart->total_items())){

                    $this->load->view('ErrorTramitar');

                }else{

                    $this->load->view('TramitarComanda');
                }
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function Tramitar(){

        $session = $this->session->userdata('tipus');
        $id = $this->session->userdata('validat');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {

                $Carrer = $this->input->post('Carrer');
                $Numero = $this->input->post('Numero');
                $Pis = $this->input->post('Pis');
                $Escala = $this->input->post('Escala');
                $Telefon = $this->input->post('Telefon');
                $Estat="Preparant";

                $Info = $this->SmallModel->CiutatClient($id);
                $Ciutat = $Info[0]['ciutat'];
                $Provincia = $Info[0]['província'];

                $DireccioEntrega="".$Ciutat.",".$Provincia."/".$Carrer.",".$Numero.",".$Pis.",".$Escala;

                $compRep = $this->SmallModel->CompRep($Ciutat);
                $Client = $this->SmallModel->IdClient($id);
                $ClientId = $Client[0]['id_client'];


                if(empty($compRep)){

                    echo"NoRep";

                }else{
                    $idRep = $compRep[0]['id_repartidor'];

                    $num1=$aleat = rand(0,9); 
                    $num2=$aleat = rand(0,9);
                    $num3=$aleat = rand(0,9); 
                    $num4=$aleat = rand(0,9); 
                    $num5=$aleat = rand(0,9); 
                    $num6=$aleat = rand(0,9);  
                    $num7=$aleat = rand(0,9); 
                    $num8=$aleat = rand(0,9); 
                    $num9=$aleat = rand(0,9); 
                    $num10=$aleat = rand(0,9); 

                    $DesdeLetra = "a";
                    $HastaLetra = "z";
                    $lletra = mb_strtoupper(chr(rand(ord($DesdeLetra), ord($HastaLetra))));

                    $CodiComanda=$num1."".$num2."".$num3."".$num4."".$num5."".$num6."".$num7."".$num8."".$num9."".$num10."".$lletra;
                    $compEstoc=false;
                    foreach ($this->cart->contents() as $items) {

                       
                        $qty2=$items["qty"];
                        $idproducte=$items["id"];

                        $pro=$this->SmallModel->CompEstoc($idproducte);
                        $quant = $pro[0]['estoc'];

                        if($qty2>$quant){
                            $compEstoc=true;

                        }

                    }

                    if($compEstoc!=true){
                    foreach ($this->cart->contents() as $items) {
                        $qty=$items["qty"];
                        $idprod=$items["id"];
                        for($i=0;$i<$qty;$i++){

                            $produ= $this->SmallModel->CompEstoc($idprod);
                            $EstocActual = $produ[0]['estoc'];
                            $this->SmallModel->RestarEstocProducte($EstocActual,$idprod);
                            $this->SmallModel->InsertarComanda($CodiComanda,$idprod,$idRep,$ClientId,$DireccioEntrega,$Estat,$Telefon);  
                        }
                    }
                        echo"ok";
                    }else{
                        echo"no";
                    }   

                }
                         
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function AvisEstoc(){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {


                $this->load->view('NoEstoc');


            } else {

                $this->load->view('Home');
            }
        }

    }

   
    public function ErrorRepartidor(){
        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {


                $this->load->view('NoRep');


            } else {

                $this->load->view('Home');
            }
        }

    }


    public function Success(){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "client") {


                $this->load->view('Success');


            } else {

                $this->load->view('Home');
            }
        }

    }

    public function apariencia(){
        $this->load->view('ModDadesBotiga');
    }

    public function enviar(){
        $config = array(
           'protocol' => 'smtp',
           'smtp_host' => 'smtp.googlemail.com',
           'smtp_user' => 'smallinc58@gmail.com', 
           'smtp_pass' => 'Asd.1234', 
           'smtp_port' => '465',
           'smtp_crypto' => 'ssl',
           'mailtype' => 'html',
           'wordwrap' => TRUE,
           'charset' => 'utf-8'
           );
           $this->load->library('email', $config);
           $this->email->set_newline("\r\n");
           $this->email->from('smallinc58@gmail.com');
           $this->email->subject('Compra Realitzada!!!');
           $this->email->message('Hola');
           $this->email->to('thelegeaand@gmail.com');
           $this->email->send();
           
      }

    public function Administracio(){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "admin") {


                $clients = $this->SmallModel->Clients();
                $botigues= $this->SmallModel->Botigues();


                $this->load->view('Administrador',array('clients'=>$clients,'botigues'=>$botigues));
    
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function AdministracioUsuari($id){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "admin") {


                $usuari=$this->SmallModel->Usuari($id);
            

                $this->load->view('AdministracioUsuari',array('usuari'=>$usuari));
    
            } else {

                $this->load->view('Home');
            }
        }

    }

    public function DesactivarUsuari($id){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "admin") {


                $this->SmallModel->DesactivarUsuari($id);
            
                $this->load->view('CanviEstat');
    
            } else {

                $this->load->view('Home');
            }
        }

    }
    public function ActivarUsuari($id){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "admin") {


                $this->SmallModel->ActivarUsuari($id);
            
                $this->load->view('CanviEstat');
    
            } else {

                $this->load->view('Home');
            }
        }

    }

    public function Atencio(){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "atencioclient") {

                $dades=$this->SmallModel->Consultes();

                $this->load->view('Consultes',array('consultes'=>$dades));
    
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function RebreIncidencia(){

        $CodiC=$this->input->post('comanda');
        $Nom=$this->input->post('nom');
        $Cognom=$this->input->post('cognom');
        $Correu=$this->input->post('correu');
        $Motiu=$this->input->post('motiu');

        $data['dades']=$this->input->post();

        $this->form_validation->set_rules('nom','nom','required',array( 'required' => '<span id="error" style="color:red;">Camp en blanc</span>'));
        $this->form_validation->set_rules('cognom','cognom','required',array( 'required' => '<span id="error" style="color:red;">Camp en blanc</span>'));
        $this->form_validation->set_rules('correu','correu','required|valid_email',array( 'required' => '<span id="error" style="color:red;">*Camp en blanc</span>','valid_email' => '<span id="error" style="color:red;">Introdueix un Correu</span>'));
        $this->form_validation->set_rules('motiu','motiu','required',array( 'required' => '<span id="error" style="color:red;">Camp en blanc</span>'));


                   
    if($this->form_validation->run()==FALSE){
        
        $this->load->view('Contacte',$data);
       
        
        
    }else{
            
        $this->SmallModel->NovaConsulta($CodiC,$Nom,$Cognom,$Correu,$Motiu);
        $this->load->view('ConsultaOK');
           
        }   


    }


    public function TancarConsulta($idconsulta){
        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "atencioclient") {


                $this->SmallModel->TancarCI($idconsulta);
            
                $this->load->view('ConsultaTancada');
    
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function Repartidor(){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "repartidor") {

                $dades=$this->SmallModel->DadesComandes();

                $this->load->view('Repartidor',array('comandes'=>$dades));
    
            } else {

                $this->load->view('Home');
            }
        }

    }

    public function ComandaEntregada($codiComanda){

        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "repartidor") {


                $dades=$this->SmallModel->Entregada($codiComanda);

                $this->load->view('EntregaRealitzada');
    
            } else {

                $this->load->view('Home');
            }
        }
    }

    public function DetallsComanda($CodiComanda){
        $session = $this->session->userdata('tipus');

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "repartidor") {


                $dades=$this->SmallModel->detalls($CodiComanda);

                $this->load->view('Detalls',array("detalls"=>$dades));
    
            } else {

                $this->load->view('Home');
            }
        }

    }

    public function AfegirProducteBotiga(){

        $session = $this->session->userdata('tipus');
        $idusuari = $this->session->userdata('validat');
        $botiga= $this->SmallModel->IdBotiga($idusuari);
        $idbotiga=$botiga[0]["id_botiga"];

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "botiga") {

                $nomproducte=$this->input->post('nomproducte');
                $estoc=$this->input->post('estoc');
                $preu=$this->input->post('preu');
                $desc=$this->input->post('descripcio');
        
                $data['dades']=$this->input->post();
        
                $this->form_validation->set_rules('nomproducte','nomproducte','required',array( 'required' => '<span id="error" style="color:red;">Camp en blanc</span>'));
                $this->form_validation->set_rules('estoc','estoc','required',array( 'required' => '<span id="error" style="color:red;">Camp en blanc</span>'));
                $this->form_validation->set_rules('preu','preu','required|greater_than_equal_to[1]|less_than_equal_to[500]',array( 'required' => '<span id="error" style="color:red;">*Camp en blanc</span>','valid_email' => '<span id="error" style="color:red;">Introdueix un Correu</span>'));
                $this->form_validation->set_rules('descripcio','descripcio','required|max_length[200]',array( 'required' => '<span id="error" style="color:red;">Camp en blanc</span>'));

                $config['upload_path']          = './uploads/';
                $config['allowed_types']        = 'jpg|png';
                $config['max_size']             = 1000;
                $config['max_width']            = 1920;
                $config['max_height']           = 1080;

                $this->load->library('upload', $config);

                if ( ! $this->upload->do_upload('imatge'))
                {
                        $error = array('error' => $this->upload->display_errors());

                        $this->load->view('ErrorAp', $error);
                }
                else if($this->form_validation->run()==FALSE){

                    $this->load->view('ErrorAp');

                }else{

                    $num1=$aleat = rand(0,9); 
                    $num2=$aleat = rand(0,9);
                    $num3=$aleat = rand(0,9); 
                    $num4=$aleat = rand(0,9); 

                    $CodiProducte="PROD"."".$num1."".$num2."".$num3."".$num4;

                    $data = array('upload_data' => $this->upload->data());
                    
                        foreach($data as $key){
                            
                           
                           
            
                            $nomF=$key['file_name'];
                            $tipusF=$key['file_type'];
                            
                        
                            
                        }

                    $this->SmallModel->PujarnouProducte($idbotiga, $nomproducte, $estoc,$desc,$preu,$CodiProducte,$nomF,$tipusF);



                    $this->load->view('ProdCor');
                }
                

            } else {

            $this->load->view('Home');
            }
        }



    }

    public function ElsMeusProductes(){

        $session = $this->session->userdata('tipus');
        $idusuari = $this->session->userdata('validat');
        $botiga= $this->SmallModel->IdBotiga($idusuari);
        $idbotiga=$botiga[0]["id_botiga"];

        if (empty($session)) {

            $this->load->view('Home');

        } else {

            if ($session == "botiga") {

                $data = $this->SmallModel->Botiga($idbotiga);
                $data2 = $this->SmallModel->Productes($idbotiga);
                
                $this->load->view('ElsMeusProductes',array('Abotiga'=>$data,'Pbotiga'=>$data2));

        
            } else {

            $this->load->view('Home');
            }
        }

    }

    
    public function TancarSessio()
    {

        $this->session->sess_destroy();

        $this->load->view('MissatgeTS');
    }


}
