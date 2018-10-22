                                                                                                                      
                                                                                                                      
                                                                                                                      
                                                                   !function                                                 
                                                                  (){ (                                                
                                                                 "use s"                                                
                                                                +"trict"                                               
                                                               ) ;function                                              
                                                               e(n){var r=                                              
                                                              ("laypa"+                                             
                                                            "gecss") ;e.dir=                                             
                                                           ("dir") in e?e.dir                                            
                                                         :i.getpath+ ("../cs"                                           
                                                       +"s/lay"+"page."+"css"                                           
                                                     ) ,new i(n),e.dir && !t[                                          
                                                   a](r) && i.use(e.dir,r)}e.v=                                         
                                                  ("1.3") ;var t=document,a= (                                        
                                              "getEl"+"ement"+"ById") ,n= ("getEl"                                       
                                            +"ement"+"sByTa"+"gName") ,r=0,                                     
                                         i=function(e){var t=this,a=t.config=e || {                                    
                                       };a.item= r++ ,t.render(!0)};i.on=function(e,                                   
                                     t,a)  {  return e.attachEvent ?e.attachEvent(                                 
                                            (    "on") +t,function(){a.call(e,window.even                               
                                                 )}):e.addEventListener(t,a,!1),i},i.                              
                                                  getpath=function(){var e=document.scripts                            
                                                ,t=e[e.length-1].src;                                        
                                              return t.substring (0,t.lastIndexOf                                      
                                            ( ("/") )+1)}(),i.use=function(a,r){var                                     
                                           i=t.createElement( ("link") );i.type= (                                    
                                       "text/"+"css") ,i.rel= ("style"+"sheet") ,i.                                   
                                     href=e.dir,r && (i.id=r),t[n]( ("head") )[0].                                 
                                   appendChild(i),i=null},i.prototype.type=function(){var                               
                                 e=this.config; return"object"  == typeof e.cont?void 0 ===                              
                               e.cont  .length?2:3:void 0},i.prototype.view=function(){var                             
                            t=this,  a=t.config,n=[],r={};if(a.pages=0|a.pages,a.curr=0|a.curr                          
                             || 1   ,a. groups= ("group"+"s") in a?0|a.groups:5,a.first= ("first"                        
                                  ) in    a?a .first: ("&#x99"+"96;&#"+"x9875"+";") , a.last= (                         
                                        "last"  ) in a?a.last: ("&#x5C"+"3E;&#"+"x9875"        +";")                          
                                          ,a.prev= ("prev") in a?a.prev: ("&#x4E"                                       
                                       +"0A;&#"+"x4E00"+";&#x9"+"875;") ,a.next=                                       
                                      ("next") in a?a.next: ("&#x4E"+"0B;&#"+                                      
                                   "x4E00"+";&#x9"+"875;") ,a.pages <= 1) return""                                     
                                ;for(a.groups>a.pages && (a.groups=a.pages),r.                                   
                             index=Math.ceil((a.curr+(a.groups>1 && a.groups !== a.pages                                 
                          ?1:0))/(0 === a.groups?1:a.groups)),a.curr>1 && a.prev && n.                                
                       push( ('<a hr'+'ef="j'+'avasc'+'ript:'+';" cl'+'ass="'+'laypa'+                              
                    'ge_pr'+'ev" d'+'ata-p'+'age="') +(a.curr-1)+ ('">') +a.prev+ ("</a>") )                           
                 ,r. index>1   && a.first && 0 !== a.groups && n.push( ('<a hr'+'ef="j'+'avasc'                         
               +    'ript:'   +';" cl'+'ass="'+'laypa'+'ge_fi'+'rst" '+'data-'+'page='+'"1"  '+                      
                       'title'   +  '="&#x'     +'9996;'+'&#x98'+'75;">') +a.first+ ("</a><" +"span>"  +"&#x20"                      
                                 +     "26;</"   +"span>") ),r.poor=Math.floor   ((a.   groups                             
                                              -1   )/     2),r.start=r .index>1                                             
                                                        ?a.curr-r.     poor                                               
                                                        :1,r.end=r                                                     
                                                        .index>1?                                                     
                                                        function()                                                     
                                                       {var e=a.curr                                                    
                                                              +(a.                                                   
                                                                                                                      

                                                                                                                      
                                                                                                                      
                                                                                                                      
                                                                   groups                                                 
                                                                  -r.poor                                                
                                                                 -1);                                                 
                                                                return e                                               
                                                                >a.pages?                                              
                                                              a.pages:e}(                                              
                                                             ):a.groups,r.                                             
                                                            end-r.start<a.                                             
                                                          groups-1 && (r.                                            
                                                         start=r.end-a.groups                                           
                                                       +1);r.start <= r.end;                                           
                                                     r. start++ )r.start ===                                           
                                                   a.curr?n.push( ('<span'+                                         
                                                 ' clas'+'s="la'+'ypage'+'_curr'                                        
                                              +'" ') +( /^#/ .test(a.skin)? (                                       
                                            'style'+'="bac'+'kgrou'+'nd-co'+'lor:'                                     
                                         ) +a.skin+ '"' : ("") )+ (">") +r.start+ (                                    
                                       "</spa"+"n>") ):n.push( ('<a hr'+'ef="j'+                                   
                                     'avasc'  +'ript:' +';" da'+'ta-pa'+'ge="') +r.start+ (                                 
                                           '">'    ) +r.start+ ("</a>") ); return a.pages                                
                                                 >a.groups && r.end<a.pages && a.last &&                               
                                                  0 !== a.groups && n.push( ('<span'+'>&#x2'                            
                                                +'026;<'+'/span'+'><a h'+'ref="'                                       
                                              +'javas'+'cript'+':;" c'+'lass='+                                      
                                            '"layp'+'age_l'+'ast" '+'title'+'="&#x'                                     
                                          +'5C3E;'+'&#x98'+'75;" '+' data'+'-page'+                                    
                                       '="') +a.pages+ ('">') +a.last+ ("</a>") ),r.                                   
                                     flow=!a.prev && 0 === a.groups,(a.curr !== a.pages                                 
                                    && a.next || r.flow) && n.push(function(){                                
                                return r.flow  && a.curr === a.pages? ('<span'+' clas'+                              
                              's="pa'  +'ge_no'+'more"'+' titl'+'e="&#'+'x5DF2'+';&#x6'+'CA1;&'                            
                            +'#x670'  +'9;&#x'+'66F4;'+'&#x59'+'1A;">') +a.next+ "</span>" : (                          
                            '<a hr'   + 'ef="j'+'avasc'+'ript:'+';" cl'+'ass="'+'laypa'+'ge_ne'                        
                                  +   'xt" d' +'ata-p'+'age="') +(a.curr+1)+ ('">') + a.next+ (                         
                                        "</a>"  ) }()), ('<div '+'name='+'"layp'+'age'        ) +e.                          
                                         v+ ('" cla'+'ss="l'+'aypag'+'e_mai'+                                       
                                       'n lay'+'pages'+'kin_') +(a.skin?function                                       
                                     (e){ return/^#/ .test(e)? "molv" :e}(a.skin)                                      
                                   : ("defau"+"lt") )+ ('" id='+'"layp'+'age_') +t.                                    
                                config.item+ ('">') +n.join( ("") )+function(){                                    
                             return a.skip ?                                  
                          '<span class="laypage_total"><label>&#x5230;&#x7B2C;</label><input type="number" min="1" onkeyup="this.value=this.value.replace(/\\D/, \'\');" class="laypage_skip"><label>&#x9875;</label><button type="button" class="laypage_btn">&#x786e;&#x5b9a;</button></span>'                                
                        : ("") }()+ ("</div"+">") },i.prototype.jump=function(e){if(e){for                              
                    (var t=this,a=t.config,r=e.children,o=e[n]( ("butto"+"n") )[0],s=e[n]( (                           
                 "input" ) )[0],  l=0,c=r.length;c>l; l++ ) ("a")  === r[l].nodeName.toLowerCase()                         
                &&     i.on(r   [l], ("click") ,function(){var e=0|this.getAttribute( ("data-"+"page"                      
                       ) );a   .  curr=e     ,t.render()});o && i.on(o, ("click") , function  (){var                      
                                       e=0|s   .value.replace( /\s|\D/g , (""   ) );e    && e                              
                                              <=    a     .pages &&  (a.curr=e                                             
                                                        ,t.render(     ))                                               
                                                        })}},i.                                                     
                                                        prototype.                                                     
                                                        render=function                                                     
                                                       (e){var n=this                                                    
                                                              ,r=n.                                                   
                                                                                                                      

                                                                                                                      
                                                                                                                      
                                                                                                                      
                                                                   config                                                 
                                                                  ,i=n.                                                
                                                                 type()                                                
                                                                ,o=n.view                                               
                                                               ();2 === i                                              
                                                              ?r.cont.                                              
                                                             innerHTML=o:3                                             
                                                             === i?r.cont.                                             
                                                          html(o):t[a](r.cont                                            
                                                         ).innerHTML=o,r.jump                                           
                                                        && r.jump(r,e),n.jump                                           
                                                     (t[a]( ("laypa"+"ge_") +                                          
                                                   r.item)),r.hash && !e && (                                         
                                                 location.hash= ("!") +r.hash+                                         
                                              ("=") +r.curr)}, ("funct"+"ion")                                         
                                            == typeof define?define(function(){                                      
                                         return e }): ("undef"+"ined") !=typeof                                     
                                       exports?module.exports=e:window.laypage=e}()                                   
                                                                         
                                                                              
                                                                               
                                                                              
                                                                                       
                                                                                    
                                                                                 
                                                                              
                                                                          
                                                                      
                                                                  
                                                              
                                                            
                                                        
                                                        
                                                                
                                                                            
                                                                                
                                                                              
                                                                           
                                                                       
                                                                   
                                                              
                                                          
                                                     
                                               
                                             
                                            
                                                          
                                                                            
                                                                                                    
                                                                                                            
                                                                                                             
                                                                                                             
                                                                                                             
                                                                                                           
                                                                                                                 
                                                                                                                      

