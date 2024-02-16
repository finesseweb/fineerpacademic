/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * created and maintaed by ashutosh
 */
$(document).ready(function(){
    
    $('.custom_radio>i').remove();
 $('.custom_radio').each(function (e) {
        if($(this).next('input').attr("checked")){
           $(this).append('<i class="glyphicon glyphicon-ok"></i>');
        }
 });

$('.custom_radio').click(function () {
 $('.custom_radio>i').remove();
    $('.custom_radio').each(function () {
        $(this).next('input').removeAttr('checked');
    });

    $(this).append('<i class="glyphicon glyphicon-ok"></i>');
    $(this).next('input').attr('checked', true);
});



$('input[type="checkbox"]').each(function(){
    if($(this).prop('checked')){
         $(this).prev('label').append('<i class="glyphicon glyphicon-ok"></i>');
    } 
});




  
$('body').on('click','input[type="checkbox"]',function(){
    if($(this).prop('checked')){
          $(this).prev('label').append('<i class="glyphicon glyphicon-ok"></i>');   
    }
    else{
         $(this).prev('label').children('i').remove();
    }
    
});

});