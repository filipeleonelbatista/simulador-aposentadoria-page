/* Javascript Customizado para Simulador de Calculo de Aposentadoria    */
/* Criador            : W Mendes Marketng Digital                       */
/* Site               : https://www.wmendes.com                         */
/* Última modificação : 26/08/2020                                      */

jQuery(document).ready(function($){
    $('.genero-click').click(
        function(event){
            event.preventDefault();
            $('.genero-click').removeClass('ativo');
            $(this).addClass('ativo');
            $('.genero').val('homem');
            if($(this).data('genero')=='mulher')
                $('.genero').val('mulher');
            })
            
})

function calcular() {
  var x = document.getElementById("enviar");
    var a = document.forms["formsimulador"]["idade"].value;
    var b = document.forms["formsimulador"]["contrib"].value;
    if (a === null || a === "", b === null || b === "") {
      alert("Por favor, preencha todos os campos.");
      event.preventDefault();
      return false;        
    }else{    
        event.preventDefault();
        x.style.display = "block";
    }
} 
