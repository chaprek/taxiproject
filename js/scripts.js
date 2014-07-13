   $(document).ready(function(){
            $('.orders_pd div').each(function(){
                $(this).animate({
                height: $(this).attr('data-height')*10+"px"
                }, 1500); 
                $(this).append("<p>"+$(this).attr('data-height')+"</p>");
            });
            
            $('#phone, #os, #sys, #city_phone_use').focus(function(){
            
             if (this.value == this.defaultValue) { 
                this.value = "";
                this.style.color = '#000' 
                } 
               }); 
               
             $('#phone, #os, #sys, #city_phone_use').blur(function(){
            
                if (this.value == "") { 
                this.value = this.defaultValue 
                this.style.color = '#A7ACAF'
                } 
               });
               
              $('.filtrs span').each(function(){
                
                  $(this).click(function(){
                    $('.filtrs span').each(function(){
                        $(this).removeClass('act');
                    });
                    $(this).addClass('act');
                    $('.display_filtrs').each(function(){
                        $(this).css('display', 'none');
                    });
                    $('#'+$(this).attr('data-filtr')+'').fadeIn();
                    
                    
                  }); 
              
              }); 
                               
                 $('.menu > div').click(function(){
                    $(".menu > div").removeClass('act');
                    var clas = $(this).attr("class");
                    var id = $(this).attr("data-menu");
                    var text = $(this).attr("data-text");
                    $(this).addClass('act');
                    
                    $(".filtrs").fadeOut();
                    $("#"+ clas).fadeIn();
                    
                    $(".show_table").attr('href', '?table='+id);
                    $(".show_table p").text(text);
                });
                
                
                
                function bind_click(el){
                    
                     $('.edit').each(function(){
                        
                        if($(this).find('.change').length > 0){
                            
                            $(this).text('Редактировать');
                            
                            var ed = $(this).parent().find('td:not(.edit)');
                            
                            ed.each(function(i) {
                                if(i != 0){
                                    $(this).html($(this).find('input').val());
                                } else {
                                    $(this).html($(this).find('input').val());
                                    $(this).find('input').remove();
                                }
                            
                            });
                            $(this).bind("click",function(){
                    
                               bind_click($(this));
                            
                            });
                        }
                            
                    });
                    
                                        
                    el.html('<p class="change">Изменить</p>');
                    
                    var tdd = el.parent().find('td:not(.edit)');
                    
                    tdd.each(function(i) {
                        if(i != 0){
                            $(this).html('<input value="'+$(this).text()+'" type="text" name="'+$(this).data('id')+'" />');
                        } else {
                            $(this).html('<input type="hidden" value="'+$(this).text()+'" name="'+$(this).data('id')+'" />' + $(this).text());
                        }
                    
                    });
                    el.unbind('click');
                }
                
                
                
                $('.edit').bind("click",function(){
                    
                   bind_click($(this));
                
                });
                
               $('.change').live('click', function(){
                    $(this).parents('form').find('.send').trigger('click');
                });
                
        });