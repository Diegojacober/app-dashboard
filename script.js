$(document).ready(() => {
    //o load() por padrão executa o metodo get
    $('#index').on('click',()=>{

        $('body').load('index.html')
      
    })



	$('#documentacao').on('click',()=>{

        $.get('documentacao.html', data => {
            $('#pagina').html(data)
        })
      // $('#pagina').load('documentacao.html')
    })



    $('#suporte').on('click',()=>{

        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
       // $('#pagina').load('suporte.html')
        
    })


    //ajax

    $('#competencia').on('change',e=>{
        
      let competencia =  $(e.target).val()

        $.ajax({
            //objeto literal
            //metodo,url,dados,sucesso,erro
            type: 'GET',
            url: 'app.php',
            data: `competencia=${competencia}`,//x-www-form-urlencoded
            dataType: 'json',
            success:dados=>{ console.log(dados) 
            $('#totalDeVendas').html(dados.total_de_vendas)
            $('#nmrDevendas').html(dados.numero_de_vendas)
            $('#clientesativos').html(dados.clientes_ativos)
            $('#clientesinativos').html(dados.clientes_inativos)},
            error:erro=>{ console.log(erro) }

        })
    })
})