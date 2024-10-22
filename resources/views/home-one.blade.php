@extends('templates.material.main')

@section('jquery') {{-- Including this section to override it empty. Using jQuery from webpack build --}} @endsection

{{-- SCRIPTS --}}
@push('before-scripts')

    {{-- <script src="{{ mix('/js/home-one.js') }}"></script> --}}
    <!-- Editable CSS -->
    <link type="text/css" rel="stylesheet" href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jsgrid/jsgrid.min.css" />
    <link type="text/css" rel="stylesheet" href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jsgrid/jsgrid-theme.min.css" />
    <!-- TouchSpin -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">
@endpush
@push('after-scripts')
    <!-- QZ-Impressora JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/qz/qz-tray.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/dependencies/rsvp-3.1.0.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/dependencies/sha-256.min.js"></script>
    <!-- Calendar JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/calendar/jquery-ui.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/moment/moment.js"></script>
    <script src='/vendor/wrappixel/material-pro/4.1.0/assets/plugins/calendar/dist/fullcalendar.min.js'></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/calendar/dist/cal-init.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/calendar/dist/jquery.fullcalendar.js"></script>
    <!-- chartist chart -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/chartist-js/dist/chartist.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/echarts/echarts-all.js"></script>
    <!-- sparkline chart -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/material/js/dashboard4.js"></script>
    <!-- Vector map JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/vectormap/jquery-jvectormap-us-aea-en.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/material/js/dashboard3.js"></script>
    <!-- Sweet-Alert  -->
    <!-- <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/sweetalert/sweetalert.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/sweetalert/jquery.sweet-alert.custom.js"></script> -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/sweetalert/sweetalert2.all.min.js"></script>
    <!-- toast  -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/toast-master/js/jquery.toast.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/material/js/toastr.js"></script>
        <!-- Editable -->
        <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jsgrid/db.js"></script>
        <script type="text/javascript" src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jsgrid/jsgrid.min.js"></script>
        <script src="/vendor/wrappixel/material-pro/4.1.0/material/js/jsgrid-init.js"></script>
    <!-- TouchSpin -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-touchspin/dist/jquery.bootstrap-touchspin.js" type="text/javascript"></script>
    <!-- jQuery peity -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/tablesaw-master/dist/tablesaw.jquery.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/tablesaw-master/dist/tablesaw-init.js"></script>

@endpush

{{-- STYLES --}}
@push('before-styles')

    <!-- chartist CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/chartist-js/dist/chartist.min.css" rel="stylesheet">
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/chartist-js/dist/chartist-init.css" rel="stylesheet">
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css" rel="stylesheet">
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/css-chart/css-chart.css" rel="stylesheet">
    <!-- Vector CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <!-- Calendar CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/calendar/dist/fullcalendar.css" rel="stylesheet" />
    <!--alerts CSS -->
    <!-- <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/sweetalert/sweetalert.css" rel="stylesheet" type="text/css"> -->
    <!-- toast CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/toast-master/css/jquery.toast.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" class="holderjs">
    <!-- Bootstrap Core CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-table/dist/bootstrap-table.min.css" rel="stylesheet" type="text/css" />
    <!-- Bootstrap responsive table CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/tablesaw-master/dist/tablesaw.css" rel="stylesheet">

@endpush

{{-- Cadastro Distribuidor --}}
@push('before-styles')
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/switchery/dist/switchery.min.css" rel="stylesheet" />
    <!-- Bootstrap Core CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/wizard/steps.css" rel="stylesheet">
@endpush

@push('after-scripts')

    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/switchery/dist/switchery.min.js"></script>
    <!--Custom JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/moment/min/moment.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/wizard/jquery.steps.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/wizard/jquery.validate.min.js"></script>
    <!--MaskMoney-->
    <script src="https://igorescobar.github.io/jQuery-Mask-Plugin/js/jquery.mask.min.js"></script>

    <!-- Editable -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jquery-datatables-editable/jquery.dataTables.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/datatables/dataTables.bootstrap.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/tiny-editable/mindmup-editabletable.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/tiny-editable/numeric-input-example.js"></script>
    <!--Custom JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-table/dist/bootstrap-table.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-table/dist/bootstrap-table.ints.js"></script>


@endpush
@push('after-scripts')
    <script src="\vendor\wrappixel\material-pro\4.1.0\material\js\jquery.PrintArea.js" type="text/JavaScript"></script>
    <!-- <script>
        $(document).ready(function() {
            $("#print").click(function() {
                var mode = 'iframe'; //popup
                var close = mode == "popup";
                var options = {
                    mode: mode,
                    popClose: close
                };
                $("div.printableArea").printArea(options);
            });
        });
    </script> -->
@endpush
<!-- DATEPICKER CLOCKPICKER TIMEPICKER ...-->
@push('after-scripts')
    <script src="/vendor/wrappixel/material-pro/4.1.0/material/js/validation.js"></script>
    <script>
        ! function(window, document, $) {
            "use strict";
            $("input,select,textarea").not("[type=submit]").jqBootstrapValidation(), $(".skin-square input").iCheck({
                checkboxClass: "icheckbox_square-green",
                radioClass: "iradio_square-green"
            }), $(".touchspin").TouchSpin(), $(".switchBootstrap").bootstrapSwitch();
        }(window, document, jQuery);
    </script>
@endpush
@push('before-styles')
    <!-- Page plugins css -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/clockpicker/dist/jquery-clockpicker.min.css" rel="stylesheet">
    <!-- Color picker plugins css -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jquery-asColorPicker-master/css/asColorPicker.css" rel="stylesheet">
    <!-- Date picker plugins css -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <!-- Daterange picker plugins css -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
@endpush
@push('after-scripts')

    <!-- Plugin JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/moment/moment.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    <!-- Clock Plugin JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/clockpicker/dist/jquery-clockpicker.min.js"></script>
    <!-- Color Picker Plugin JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jquery-asColorPicker-master/libs/jquery-asColor.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jquery-asColorPicker-master/libs/jquery-asGradient.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/jquery-asColorPicker-master/dist/jquery-asColorPicker.min.js"></script>
    <!-- Date Picker Plugin JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/moment/moment.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>
        <script>
        $('.navtoggler').on("click", function(){
            var search='';
        });
        var mobile = false;
        if (/mobile/i.test(navigator.userAgent)) {
            var mobile = true;
            //remover menu ao selecionar opção
            $('.navtoggler').on("click", function(){
                $("body").removeClass("show-sidebar");
            });
            // toggle MenuLateral sidebar navtoggler
            var ts_x;
            var ts_y;
            document.addEventListener('touchstart', function(e) {
                e.preventDefault();
                var touch = e.changedTouches[0];
                ts_x = touch.pageX;
                ts_y = touch.pageY;
            }, false);
            document.addEventListener('touchmove', function(e) {
                e.preventDefault();
                var touch = e.changedTouches[0];
                td_x = touch.pageX - ts_x; // deslocamento na horizontal
                td_y = touch.pageY - ts_y; // deslocamento na vertical
                // O movimento principal foi vertical ou horizontal?
                if( Math.abs( td_x ) > 150 && Math.abs( td_y ) < 75 ) {
                    // é horizontal
                    if( td_x < 100 ) {
                        $("body").removeClass("show-sidebar");
                    } else if( ts_x < 20 && td_x > 100 ) {
                        $("body").addClass("show-sidebar");
                    }
                }
            }, false);
        }
        $('#input-form-logout').val(mobile);//diferencia 'Sair' mobile true ou mobile false para zerar token fcm
        if (/android/i.test(navigator.userAgent)) {
            //remover menu ao selecionar opção
            $('.navtoggler').on("click", function(){
                $("body").removeClass("show-sidebar");
            });
        }
        /*******************************************/
        // Basic Date Range Picker
        /*******************************************/
        $('.daterange').daterangepicker();

        /*******************************************/
        // Date & Time
        /*******************************************/
        $('.datetime').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            locale: {
                format: 'MM/DD/YYYY h:mm A'
            }
        });

        /*******************************************/
        //Calendars are not linked
        /*******************************************/
        $('.timeseconds').daterangepicker({
            timePicker: true,
            timePickerIncrement: 30,
            timePicker24Hour: true,
            timePickerSeconds: true,
            locale: {
                format: 'MM-DD-YYYY h:mm:ss'
            }
        });

        /*******************************************/
        // Single Date Range Picker
        /*******************************************/
        $('.singledate').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true
        });

        /*******************************************/
        // Auto Apply Date Range
        /*******************************************/
        $('.autoapply').daterangepicker({
            autoApply: true,
        });

        /*******************************************/
        // Calendars are not linked
        /*******************************************/
        $('.linkedCalendars').daterangepicker({
            linkedCalendars: false,
        });

        /*******************************************/
        // Date Limit
        /*******************************************/
        $('.dateLimit').daterangepicker({
            dateLimit: {
                days: 7
            },
        });

        /*******************************************/
        // Show Dropdowns
        /*******************************************/
        $('.showdropdowns').daterangepicker({
            showDropdowns: true,
        });

        /*******************************************/
        // Show Week Numbers
        /*******************************************/
        $('.showweeknumbers').daterangepicker({
            showWeekNumbers: true,
        });

        /*******************************************/
        // Date Ranges
        /*******************************************/
        $('.dateranges').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        /*******************************************/
        // Always Show Calendar on Ranges
        /*******************************************/
        $('.shawCalRanges').daterangepicker({
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            alwaysShowCalendars: true,
        });

        /*******************************************/
        // Top of the form-control open alignment
        /*******************************************/
        $('.drops').daterangepicker({
            drops: "up" // up/down
        });

        /*******************************************/
        // Custom button options
        /*******************************************/
        $('.buttonClass').daterangepicker({
            drops: "up",
            buttonClasses: "btn",
            applyClass: "btn-info",
            cancelClass: "btn-danger"
        });

        /*******************************************/
        // Language
        /*******************************************/
        $('.localeRange').daterangepicker({
            ranges: {
                "Aujourd'hui": [moment(), moment()],
                'Hier': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Les 7 derniers jours': [moment().subtract('days', 6), moment()],
                'Les 30 derniers jours': [moment().subtract('days', 29), moment()],
                'Ce mois-ci': [moment().startOf('month'), moment().endOf('month')],
                'le mois dernier': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            locale: {
                applyLabel: "Vers l'avant",
                cancelLabel: 'Annulation',
                startLabel: 'Date initiale',
                endLabel: 'Date limite',
                customRangeLabel: 'SÃ©lectionner une date',
                // daysOfWeek: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi','Samedi'],
                daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve', 'Sa'],
                monthNames: ['Janvier', 'fÃ©vrier', 'Mars', 'Avril', 'ÐœÐ°i', 'Juin', 'Juillet', 'AoÃ»t', 'Septembre', 'Octobre', 'Novembre', 'Decembre'],
                firstDay: 1
            }
        });
    </script>
    <script>
        // MAterial Date picker
        $('#mdate').bootstrapMaterialDatePicker({ weekStart: 0, time: false });
        $('#timepicker').bootstrapMaterialDatePicker({ format: 'HH:mm', time: true, date: false });
        $('#date-format').bootstrapMaterialDatePicker({ format: 'dddd DD MMMM YYYY - HH:mm' });

        $('#min-date').bootstrapMaterialDatePicker({ format: 'DD/MM/YYYY HH:mm', minDate: new Date() });
        // Clock pickers
        $('#single-input').clockpicker({
            placement: 'bottom',
            align: 'left',
            autoclose: true,
            'default': 'now'
        });
        $('.clockpicker').clockpicker({
            donetext: 'Done',
        }).find('input').change(function() {
            console.log(this.value);
        });
        $('#check-minutes').click(function(e) {
            // Have to stop propagation here
            e.stopPropagation();
            input.clockpicker('show').clockpicker('toggleView', 'minutes');
        });
        if (/mobile|android/i.test(navigator.userAgent)) {
            $('input').prop('readOnly', true);
        }
        // Colorpicker
        $(".colorpicker").asColorPicker();
        $(".complex-colorpicker").asColorPicker({
            mode: 'complex'
        });
        $(".gradient-colorpicker").asColorPicker({
            mode: 'gradient'
        });
        // Date Picker
        jQuery('.mydatepicker, #datepicker').datepicker();
        jQuery('#datepicker-autoclose').datepicker({
            autoclose: true,
            todayHighlight: true
        });
        jQuery('#date-range').datepicker({
            toggleActive: true
        });
        jQuery('#datepicker-inline').datepicker({
            todayHighlight: true
        });


        // Daterange picker
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse'
        });
        $('.input-daterange-timepicker').daterangepicker({
            timePicker: true,
            format: 'MM/DD/YYYY h:mm A',
            timePickerIncrement: 30,
            timePicker12Hour: true,
            timePickerSeconds: false,
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse'
        });
        $('.input-limit-datepicker').daterangepicker({
            format: 'MM/DD/YYYY',
            minDate: '06/01/2015',
            maxDate: '06/30/2015',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            dateLimit: {
                days: 6
            }
        });
    </script>
@endpush
<!-- ...DATEPICKER CLOCKPICKER TIMEPICKER -->
<!-- UPLOAD DE ARQUIVOS... -->
@push('before-styles')
    <link rel="stylesheet" href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/dropify/dist/css/dropify.min.css">
@endpush
@push('after-scripts')
    <script src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/dropify/dist/js/dropify.min.js"></script>
@endpush
<!-- ...UPLOAD DE ARQUIVOS -->

@push('after-scripts')
<!--PLACES MAPA GOOGLE-->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIt2CSa_K8P64daT3v4Hv8Ml-8IJsFic8&libraries=places"></script>

<!--FIREBASE-->
    <!-- The core Firebase JS SDK is always required and must be listed first -->
    <script src="https://www.gstatic.com/firebasejs/7.1.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.1.0/firebase-messaging.js"></script>

    <!-- TODO: Add SDKs for Firebase products that you want to use
        https://firebase.google.com/docs/web/setup#available-libraries -->

    <script>
    const firebaseConfig = {
        apiKey: "AIzaSyDIt2CSa_K8P64daT3v4Hv8Ml-8IJsFic8",
        authDomain: "tokumsede.firebaseapp.com",
        databaseURL: "https://tokumsede.firebaseio.com",
        projectId: "tokumsede",
        storageBucket: "tokumsede.appspot.com",
        messagingSenderId: "1062632785302",
        appId: "1:1062632785302:web:271e169878115e002149b7"
    };
    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();

    // Add the public key generated from the console here.
    messaging.usePublicVapidKey("BH3ws8YH_KvBeXJHQwnskocyaQJZAVOKrwgn0-6zkMT42EM8OTS7YisiOOU8VnZlGj0Xny-BErnLFh4kxMyrpv0");

    messaging.requestPermission().then(function(){
        console.log('PERMITIDO');
        messaging.getToken().then((token) => {
            console.log('TOKEN: ',token);
            //*envia dados do formulário
            axios.post('token',{
                token: token,
                mobile: mobile
            }).then(response => {
                console.log('TOKEN ENVIADO');
            }).catch(error =>{
                console.log(JSON.stringify(error));
            });
        }).catch(function(){
            console.log('NO TOKEN: ',token);
        });
    }).catch(function(){
        console.log('NÃO PERMITIDO');
    });
    messaging.onMessage(function(payload){
        console.log('onMessage: ',payload);
        if(payload.notification.tag.match(/Produto/)){
            alert(payload.notification.title+". "+payload.notification.body);
        }else if(payload.notification.tag.match(/Compra/)){
            Swal.fire({
                type: 'info',
                title: payload.notification.title,
                text: payload.notification.body,
                showCancelButton: true,
                confirmButtonText: "<i class='fas fa-shipping-fast'></i> Listar Compras",
                cancelButtonText: "Agora não"
            }).then((result) => {
                if(result.value){
                    window.location="https://adm.tokumsede.com/#/listacompras";
                }
            });
        }else{
            if(window.location.href!='https://adm.tokumsede.com/#/listapedidos'){
                if(audio){audio.play();}
                $("#alertaPedidoTop").addClass("notify");
                $("#alertaTopbar").html("<i class='fas fa-bell'></i> +Pedidos");
                $("#audio").addClass("d-none");
                $("#alertaTopbar").removeClass("d-none");
            }else{
                if(audio){audio.play();}
                $("#alertaPedido").addClass("notify");
                $("#abaPedidosPendentes").html("<span class='hidden-md-up'><i class='fas fa-bell'></i></span><span class='hidden-sm-down'><i class='fas fa-bell'></i> + Pendentes</span>");
                //window.navigator.vibrate([100,30,100,30,100,30,200,30,200,30,200,30,100,30,100,30,100]);
            }
        }
    });
    </script>
@endpush
<!-- MULTISELECT P COMPOSIÇÕES -->
@push('before-styles')
    <!-- chartist CSS -->
    <link href="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/multiselect/css/multi-select.css" rel="stylesheet" type="text/css" />
@endpush
@push('after-scripts')
    <script type="text/javascript" src="/vendor/wrappixel/material-pro/4.1.0/assets/plugins/multiselect/js/jquery.multi-select.js"></script>
@endpush

{{-- -------------CONTENT---------------- --}}

@section('content')
<router-view></router-view>
@endsection
