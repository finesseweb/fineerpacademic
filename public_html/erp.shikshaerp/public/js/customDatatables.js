/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 var oTable = ''; 
var dt = { _datatablesSettings : {
    _colvis:false,
    _colvis_name:false,
    _pageLength:-1,
    _image_pr:false,
    _logo:false,
    _buttons:1,
    _range: 0,
    _startMenuLength:1,
    _endMenuLength:1000,
   
    _createtable: function () {
  var range = parseInt(this._range);
     $.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        console.log(range);
        var min = parseInt( $('#min').val(), 10 );
        var max = parseInt( $('#max').val(), 10 );
        var age = parseFloat(data[range]) || 0; // use data for the age column
 
        if ( ( isNaN( min ) && isNaN( max ) ) ||
             ( isNaN( min ) && age <= max ) ||
             ( min <= age   && isNaN( max ) ) ||
             ( min <= age   && age <= max ) )
        {
            return true;
        }
        return false;          
    }
);

                var valarr = [];
                var name = [];

                for (x = this._startMenuLength; x <= this._endMenuLength; x++) {
                    valarr.push(x);
                    name.push(x);
                }
                valarr.push('-1');
                name.push('All');
                    var pageLength = 10;
                    var lists = [valarr, name];

                
                    
                var title = $('.page-title>i,.page-title>div>i').text();
                 oTable = $('#datatable-responsive,#dataTable').DataTable({
                    "retrieve": true,
                    "paging": true,
                    "responsive": false,
                    "bScrollCollapse": true,      
                    "lengthChange": true,
                    "pageLength":this._pageLength,
                     fixedHeader: {
            header: true,
            },
                    dom: 'lBfrtip',
                    buttons: [
                        {
                            extend: 'copy',
                            text: '<b class="btn btn-xs text-primary"><strong><span class="fa fa-files-o"></span> Copy</strong> </b>',
                            filename: title + '(' + day + '-' + month + '-' + year + ')',
                            exportOptions: {
                                columns: "thead th:not(.no_print)",
                                modifier: {
                                    page: 'current'
                                }
                            },
                        },
                        {
                            extend: 'csv',
                            text: '<b class="btn  btn-xs text-primary"><strong><span class="fa fa-file-excel-o"></span> Excel </strong></b>',
                            customize: function (xlsx) {
                                var totalLen = parseInt(($('.complex').length ));
                                 var multiple = 1;
                                 var str = [];
                                 if(totalLen>1){
                                $('.complex').each(function (index, tr) {
                                    if (index < (totalLen)) {
                                             if($(this).data('id')!=='no_print'){
                                            if($(this).data('id')=='hide'){
                                                str.push('""'); 
                                            }
                                            else{
                                                str.push('"'+$(this).text()+'"');
                                            }
                                        }
                                        
                                    }
                                });
                            }
                            str.join(',');
                          return str+'\n'+xlsx;     
                        },
                            filename: title + '(' + day + '-' + month + '-' + year + ')',
                            exportOptions: {
                                columns: "thead th:not(.no_print)",
                                "columnDefs": [
                                    {"width": "20%", "targets": 0}
                                ],
                                modifier: {
                                    page: 'current'
                                }
                            }
                        },
                        {
                            extend: 'print',
                            text: '<b  class="btn  btn-xs text-primary"><strong><span class="fa fa-print"></span> Print </strong></b>',
                            title: "",
                             orientation: 'landscape', //landscape /portrait
                            pageSize: 'A3',
                            customize: function (win) {
                                $(win.document.body)
                                        .css('font-size', '10pt')
                                        .prepend(
                                                '<img src="'+this._logo+'" style="position:fixed; top:20%; left:20%;   opacity: 0.1; height:50%" />'
                                                );

                                $(win.document.body).find('table')
                                        .addClass('compact')
                                        .css('font-size', 'inherit');
    
                                
                                 $(win.document.body ).find('.no_print').css('display', 'none');


                                $(win.document.body).find('thead').prepend("<tr id='firstHeader'></tr>");
                                
                                
                                var totalLen = parseInt(($('.complex').length ));
                                var colspan = parseInt($('thead>tr>th').length/2);
              $(win.document.body).find('thead').prepend("<tr><th style='text-align:center;' colspan ='"+colspan+"' id='pwcprintheader'></th></tr>");
              $(win.document.body).append("<div style='position:fixed; left:4%;  bottom:0;width:100%' id='pwcprintfooter'></div>");
//                            
                                if(totalLen>1){
                                $('.complex').each(function (index, tr) {
                                    if (index < (totalLen )) {
                                                if($(this).data('id')!=='no_print'){
                                                    
                                                 if( $(this).data('id')=='hide'){
                                                       $(win.document.body).find('#firstHeader').append('<th></th>');
                                                 }else{
                                                        $(win.document.body).find('#firstHeader').append('<th>' + $(this).text() + '</th>');
                                                }
                                               }
                                        
                                    }
                                    
                                });
                                
                                
                                
                                //=======[For Header]=====================//
                  $(win.document.body).find("#pwcprintheader").append("<span style='font-size:2em'><b>PATNA WOMEN'S COLLEGE</b></span><br/>");
                  $(win.document.body).find("#pwcprintheader").append("<span>AUTONOMOUS</span><br/>");
                  $(win.document.body).find("#pwcprintheader").append("<span>3rd Cycle NAAC Accredited at A Grade with CGPA 3.58/4</span><br/>");
    $(win.document.body).find("#pwcprintheader").append("<span>'College with Potential for Excellence&#39; (CPE) status accorded by UGC</span><br/>");
                  $(win.document.body).find("#pwcprintheader").append("<span>BAILEY ROAD, PATNA - 800 001, BIHAR</span><br/><br/>");
                  $(win.document.body).find("#pwcprintheader").append("<span>TABULATION REGISTER</span><br/>");
                  $(win.document.body).find("#pwcprintheader").append("<span>Department : "+dept+"</span><br/>");
                                
                                //=============[For footer]===================//
                            
$(win.document.body).find("#pwcprintfooter").append("<span style='font-size:1.5em; width:33%;float:right;'><b>Tabulator</b></span>");
$(win.document.body).find("#pwcprintfooter").append("<span style='font-size:1.5em; width:33%;float:right;'><b>Controller of Examinations</b></span>");
$(win.document.body).find("#pwcprintfooter").append("<span style=' font-size:1.5em; width:33%;float:right;'><b>Principal</b></span>");
                            }

                            },
                            exportOptions: {
                                columns: "thead tr th:not(.no_print)",
                                modifier: {
                                    page: 'current'
                                }
                            }
                        }, {
                            extend: 'pdf',
                            orientation: 'portrait', //landscape /portrait
                            pageSize: 'A3',
                            text: '<b class="btn btn-xs text-primary"><strong><span class="fa fa-file-pdf-o"></span> PDF </strong></b>',
                            filename: title + '(' + day + '-' + month + '-' + year + ')',
                            exportOptions: {
                                columns: "thead th:not(.no_print)",
                                search: 'applied',
                                order: 'applied',
                                modifier: {
                                    page: 'current',
                                    alignment: 'center'
                                }
                            }
                        },
                        {
                            extend: this._colvis,
                            text: this._colvis_name,
                        }

                    ],
                    "lengthMenu": lists
                });
                

     
    // Event listener to the two range filtering inputs to redraw on input
   
 $('<div class="row" id="moved-visibility"  style="float:left;border:1px solid #000"></div>').appendTo('#datatable-responsive_filter,#dataTable_filter');
            $('a.buttons-colvis').appendTo("#moved-visibility");
            $('<div class="row" id="moved-buttons" style="margin-top:1em; margin-right:.09em;"></div>').appendTo('#datatable-responsive_filter,#dataTable_filter');
$('div[class="dt-buttons btn-group"]').appendTo('#moved-buttons');
$('select[name="dataTable_length"],select[name="datatable-responsive_length"]').select2();
if(this._buttons == '0'){
   $('#moved-buttons').remove();
}
            }
            
            }
        };


