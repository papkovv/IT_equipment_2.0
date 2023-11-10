$(document).ready(function () {
    // let owl = $(".equip_gallery .gallery_items");
    // owl.owlCarousel({
    //     items: 4,
    //     loop: true,
    //     center: true,
    //     nav: false,
    //     dots: false,
    //     margin: 10,
    //     // URLhashListener: true,
    //     autoplayHoverPause: true,
    //     // startPosition: 'URLHash',
    //     autoHeight: false,
    //     autoHeightClass: 'owl-height',
    //     navText: ["<img src='/local/templates/avtodor/img/icon/ArrowPrev.svg' />", "<img src='/local/templates/avtodor/img/icon/ArrowNext.svg' />"]
    // });
    $('.equip_gallery .gallery_items').slick({
        infinite: true,
        slidesToShow: 4,
        slidesToScroll: 1
      });
    //   $('.equip_gallery .gallery_items').slick('unslick').slick('reinit').slick();
    //   $('.equip_gallery .gallery_items').on('init', function(event, slick){
    //     console.log("initialised")
    //     $('.equip_gallery .gallery_items').slick()
    //   });

    $('.searchbox__btn').on('click', function () {
        $('.searchbox__input input, .searchbox__btn').removeClass('active');
        let index = $(this).index();
        $('.searchbox__input input:eq(' + index + '), .searchbox__btn:eq(' + index + ')').addClass('active');
    });

    $('.favorites__more').on('click', function () {
        let btn = $(this);
        if (btn.hasClass('active')) {
            btn.removeClass('active');
            $('.favorites__items').css('max-height', '135px');
        } else {
            btn.addClass('active');
            $('.favorites__items').css('max-height', '500px');
        }
    });

    $('.block__title .tab li').on('click', function () {
        $('.event__items, .block__title .tab li').removeClass('active');
        let index = $(this).index();
        $('.event__items:eq(' + index + '), .block__title .tab li:eq(' + index + ')').addClass('active');
    });

    $('.widget__birthday__date__block .tab li').on('click', function () {
        $('.widget__birthday__date__items, .widget__birthday__date__block .tab li').removeClass('active');
        let index = $(this).index();
        $('.widget__birthday__date__items:eq(' + index + '), .widget__birthday__date__block .tab li:eq(' + index + ')').addClass('active');
    });
    $('.header__group_btn a').on('click', function () {
        let btn = $(this);
        if (btn.hasClass('active')) {
            btn.removeClass('active');
            $('.menu:eq(' + btn.index() + ')').removeClass('active');
            $('body').css({
                height: 'auto',
                overflow: 'unset'
            });
        } else {
            $('.menu, .header__group_btn a').removeClass('active');
            btn.addClass('active');
            $('.menu:eq(' + btn.index() + ')').addClass('active');
            $('body').css({
                height: '100vh',
                overflow: 'hidden'
            });
        }
    });

    $('.menu__group__btn a').on('click', function () {
        let btn = $(this);
        let index = btn.index();
        $('.menu__group__btn a, .search__box, .menu .searchbox__input input').removeClass('active');
        btn.addClass('active');
        $('.menu:eq(0) .searchbox__input input:eq(' + index + '), .search__box:eq(' + index + ')').addClass('active');

    });

    $('.accordion__item').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $('.accordion__item').removeClass('active');
            $(this).addClass('active');
        }
    });


    $(document).on("click",".equip_btn",function() {
        console.log('321')
        var $arr = [];
        var url = '?mode=edit&list_id=48&section_id=0&element_id=0&list_section_id=';
        // $(document).on("each",".main-grid-row-body",function() {
        $('.main-grid-row-body').each(function(){
            // console.log(url)
            

            // url = '?mode=edit&list_id=48&section_id=0&element_id=0&list_section_id=';
            if($(this).hasClass('main-grid-row-checked')){
                var id = '&item-id[]='+ $(this).data('id');
                url = url+id;
                var name = '&item-name[]='+$(this).find($('.main-grid-cell-left')).first().find('a').text();
                url = url+name;
                var mol_id = '&mol='+$(this).data('mol');
                url = url+mol_id;
                var fakt_id = '&fakt='+$(this).data('fakt');
                url = url+fakt_id;
                var mol_name = '&molname='+$(this).data('molname');
                url = url+mol_name;
                var fakt_name = '&faktname='+$(this).data('faktname');
                url = url+fakt_name;

                // $arr.push($(this).data('id'));
                
               
            
            }
            
        });
        console.log(url);
        console.log($(location).attr("origin"));
        var newurl = $(location).attr("origin")+'/equipment/'+ url;
        $(location).attr('href',newurl);
    });

    $('.hidden_values').each(function(){
        var name = $(this).data('name');
        var id = $(this).val();
        $('.no-js').append('<a href="/equipment/?mode=edit&list_id=47&;section_id=0&element_id='+id+'" target="_blank">['+id+']'+name+'</a>')
    }); 

    var sPageURL = new URLSearchParams(window.location.search.substring(1));
        var mol = sPageURL.get("mol");
            if(sPageURL.get("fakt") != "null"){
                fakt = sPageURL.get("fakt");
            }
            mol_name = sPageURL.get("molname");
            if(sPageURL.get("faktname") != "null"){
                fakt_name = sPageURL.get("faktname");
            }
        
            fakt_name = sPageURL.get("faktname");
            element_id = sPageURL.get("element_id");
            if(fakt_name != ''  &&  fakt != '' && element_id == '0'){
                console.log(fakt_name)
                console.log(fakt)
                $("input[name='PROPERTY_241[]']").parent().next().append('['+fakt+'] '+fakt_name+'');
                $("input[name='PROPERTY_241[]']").before('<input type="hidden" name="PROPERTY_241[]"  value="'+fakt+'">');
            }
            if(mol_name != '' &&  mol != '' && element_id == '0'){
                $("input[name='PROPERTY_312[]']").parent().next().append('['+mol+'] '+mol_name+'');
                $("input[name='PROPERTY_312[]']").before('<input type="hidden" name="PROPERTY_312[]"  value="'+mol+'">');
            }
            
            
    
    
    
            function myFunction() {
                var popup = document.getElementById("myPopup");
                popup.classList.toggle("show");
            }
    

    $('.decommission').on('click', function(){
        var id = $(this).data('id');
        console.log(id);
        $.ajax({
            type: 'POST',
            url: '/decommission.php',
            data: {ID: id},
            success: function(data) {
                
                // console.log(data);
                window.location.href = '/it-equipment-2/?mode=view&list_id=152&section_id=0&list_section_id=';
                // location.reload();
                // location.href('/equipment/?mode=view&list_id=47&section_id=0&list_section_id=');
            },
            error:  function(xhr, str){
          alert('Возникла ошибка: ' + xhr.responseCode);
            }
            
          });
    });

    $('.recover').on('click', function(){
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: '/recover_archive.php',
            data: {ID: id},
            success: function(data) {
                console.log(data);
                location.href = '/equipment/?mode=view&list_id=52&section_id=0&list_section_id=';
            },
            error:  function(xhr, str){
        //   alert('Возникла ошибка: ' + xhr.responseCode);
            }
            
          });
    });
    $('.save_file').on('click', function(){
        var data = new FormData();
        // var data = $('.update_file').serialize();
        //Form data
            var form_data = $('.update_file').serializeArray();
            $.each(form_data, function (key, input) {
                data.append(input.name, input.value);
            });

        var file_data = $('input[name="FILE"]')[0].files;
            for (var i = 0; i < file_data.length; i++) {
                data.append("FILE[]", file_data[i]);
            }

            //Custom data
// data.append('key', 'value');
        $.ajax({
            type: 'POST',
            url: '/update_file.php',
            processData: false,
            contentType: false,
            data: data,
            success: function(data) {
                console.log(data);
            // location.href('/equipment/?mode=view&list_id=47&section_id=0&list_section_id=');
            location.reload();
            },
            error:  function(xhr, str){
        //   alert('Возникла ошибка: ' + xhr.responseCode);
            }
            
          });
    });

// $('.equip_btn').on('click', function(e){
//     console.log('321')
//     $('.main-grid-row-body').each(function(){
//         console.log($(this))
//         if($(this).hasClass('.main-grid-row-checked')){
            
        
//         }
//     });
// });

	// Initialize Variables
	var closePopup = document.getElementById("popupclose");
	var overlay = document.getElementById("overlay");
	var popup = document.getElementById("popup");
	var button = document.getElementById("button");
	// Close Popup Event
	closePopup.onclick = function() {
	  overlay.style.display = 'none';
	  popup.style.display = 'none';
	};
	// Show Overlay and Popup
	button.onclick = function() {
	  overlay.style.display = 'block';
	  popup.style.display = 'block';
	}
	


var files_url = window.location.href;
$(document).on('click', '#popup .bx-disk-interface-filelist .main-grid .main-grid-row-body .main-grid-cell-checkbox', function(){
    if($(this).parents('tr').hasClass('main-grid-row-checked')){
        var fileId = $(this).find('input').val();
        var fileName = $(this).parents('.main-grid-row-body').find('.bx-disk-folder-title').text();
        var icon = $(this).parents('.main-grid-row-body').find('.bx-file-icon-container-small').attr("class");
        var type_folder = $(this).parents('.main-grid-row-body').data('is-folder');
        var type_file = $(this).parents('.main-grid-row-body').data('is-file');
        console.log(fileId)
        console.log($('.file_pick[data-id='+fileId+']').text());
        $('.file_pick[data-id='+fileId+']').parents('.file_item_container').remove();

        
    }else{
        var fileId = $(this).find('input').val();
        var fileName = $(this).parents('.main-grid-row-body').find('.bx-disk-folder-title').text();
        var icon = $(this).parents('.main-grid-row-body').find('.bx-file-icon-container-small').attr("class");
        var type_folder = $(this).parents('.main-grid-row-body').data('is-folder');
        var type_file = $(this).parents('.main-grid-row-body').data('is-file');
        var path = Array();
        $('.js-disk-breadcrumbs-folder').each(function(){
            path.push($(this).data('object-name'));
            // path = $(this).data('object-name');
        });
    
        var breadcrumbs = "";
        $.each(path,function(index,value){
            if(breadcrumbs == ''){
                breadcrumbs = value;
            }else{
                breadcrumbs = breadcrumbs+'/'+value;
            }
            
        });
        console.log(breadcrumbs)
        $('.picked_files').append("<div class='file_item_container'><div class='addclass'></div><div class='file_pick' data-is-folder="+type_folder+" data-is-file="+type_file+" data-id="+fileId+">"+fileName+"</div><div class='nav_file_item'>"+breadcrumbs+"</div><div class='file_del'>X</div></div>");
        $('.picked_files .addclass').addClass(icon);
        // if($('.file_item_container').length > 0){
        //     $('#popup .button_add_fele_on_page').css('display', 'block');
        // }else{
        //     $('#popup .button_add_fele_on_page').css('display', 'none');
        // }
    }
   
});
$(document).on('click', '#popup .file_del', function(){
    $(this).parent().remove();
    // if($('.file_item_container').length > 0){
    //     $('#popup .button_add_fele_on_page').css('display', 'block');
    // }else{
    //     $('#popup .button_add_fele_on_page').css('display', 'none');
    // }
});
$(document).on('click', '#popup #popupclose', function(){
    if(files_url != window.location.href){
        window.location.href = files_url;
    }
});
$(document).on('click', '#popup .button_add_fele_on_page', function(){
    var fileId = new Array();
    var filePath = new Array();
    var folderId = new Array();
    var isFile = new Array();
    var isFolder = new Array();
    var folderPath = new Array();
    var page = $('.file_add_popup').data('page');
    var iblock = $('.file_add_popup').data('iblock');
    console.log('111')
    $('.file_item_container').each(function(){
        
        
        isFile.push($(this).find('.file_pick').data('is-file'));
        isFolder.push($(this).find('.file_pick').data('is-folder'));
    //   $(this).find('.file_pick').data('id');
        if($(this).find('.file_pick').data('is-file') == true){
            fileId.push($(this).find('.file_pick').data('id'));
            filePath.push($(this).find('.nav_file_item').text());
        }

        if($(this).find('.file_pick').data('is-folder') == true){
            folderId.push($(this).find('.file_pick').data('id'));
            folderPath.push($(this).find('.nav_file_item').text());
        }
    console.log('data-is-file='+$(this).find('.file_pick').data('is-file'));
    });
    console.log(fileId);
    $.ajax({
        type: 'POST',
        url: '/ajax_page_file_add.php',
        data: {fileId: fileId, page: page, iblock: iblock, filePath: filePath, folderId: folderId, folderPath: folderPath},
        success: function(data) {
            $('.button_add_fele_on_page').remove();
          $('.picked_files').text('Файлы успешно добавлены!');
        //   $('.picked_files').text(data);
          setTimeout(window.location.href = files_url, 1000);

        },
        error:  function(xhr, str){
      alert('Возникла ошибка: ' + xhr.responseCode);
        }
      });
});
$('.two_tier__list>li').on('click', function(event) {
    let item = $(event.target);
    console.log(item)
    if(item.hasClass('active')){
        item.removeClass('active');
    }else{
        item.addClass('active');
    }
});
let datepicker = $('input[name="datepicker"]');
var search = document.location.search;
var searchParams = new URLSearchParams(search);
if(searchParams.has('DATESTART')){
    var dateStart = searchParams.get("DATESTART");
}
if(searchParams.has('DATESTART')){
    var dateEnd = searchParams.get("DATEEND");
}
datepicker.daterangepicker(
    {   
        startDate: dateStart,
        endDate: dateEnd,
        autoUpdateInput: false,
        showDropdowns: true,
        locale: {
            cancelClass: "cansel_custom_btn",
            cancelLabel: 'Очистить',
            applyLabel: 'Применить',
            separator : " -",
            daysOfWeek : [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"],
            monthNames : ["январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь" ],
            firstDay : 1
        }
    }
);

datepicker.on('apply.daterangepicker', function(ev, picker) {
    const opt = {month: 'long'};
    let dateEnd = new Date(picker.endDate);
    $(this).val('C ' + picker.startDate.format('DD') + ' по ' + picker.endDate.format('DD') + ' ' + dateEnd.toLocaleDateString('ru-RU', opt));
});

datepicker.on('cancel.daterangepicker', function(ev, picker) {
    datepicker.val('');
    var protocol = document.location.protocol;
    var host = document.location.host;
    var pathname = document.location.pathname;
    var docurl = protocol+host+pathname;
    var url = document.location.href;
    var search = document.location.search;
    if(pathname == '/documents/'){
        if(searchParams.has("FILE_TYPE") || searchParams.has("FOLDER_ID")){
            window.location.href = '?FOLDER_ID='+ searchParams.get("FOLDER_ID")+'&FILE_TYPE='+searchParams.get("FILE_TYPE")+'&year=all';
        }else{
            window.location.href = '/documents/';
        }
    }else if (search != "") {
        var searchParams = new URLSearchParams(search);
        if(searchParams.has("ID")){
            window.location.href = '?ID='+ searchParams.get("ID")+'&year=all';
        }else{
            window.location.href = '/events/';
        }
    }else{
        window.location.href = '/events/';
    }

});
$('.applyBtn').on('click', function(){

    var url = document.location.href;
    var search = document.location.search;
    var input_val = $(this).parent().find('.drp-selected').text();
    var dateStart = (input_val.split('-')[0]).replace(" ",""); // 50ml
    var dateEnd = (input_val.split('-')[1]).replace(" ",""); // $100

    if (search != "") {
        var searchParams = new URLSearchParams(search);
        if(searchParams.has("ID")){
            window.location.href = '?ID='+ searchParams.get("ID")+'&year=all'+ '&DATESTART='+ dateStart + '&DATEEND='+dateEnd;
        }else{
            window.location.href = '?DATESTART='+ dateStart + '&DATEEND='+dateEnd;
        }
        
     }else{
        
        console.log(dateStart)
        console.log(dateEnd)
        window.location.href = document.location.href + '?DATESTART='+ dateStart + '&DATEEND='+dateEnd;
     }
   

});

// $('.main-ui-filter-field-container-list').append('<div data-type="CUSTOM_ENTITY" data-name="PROPERTY_226" class="main-ui-filter-wield-with-label main-ui-control-field">123</div>')


})