<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 

require './functions/functions.php';

$filePath = $_FILES['file']['tmp_name'];
if (empty($_FILES) || empty($filePath)) {
    header('Location: index.php');
}
$iblockID = 152;
$file = kama_parse_csv_file($filePath);
//debug($file);
$properties = propertiesList($iblockID);

//$arFilterCur = Array("IBLOCK_ID"=>$iblockID, "PROPERTY_CODE_TMC"=>'Упр00003393');
//$resCur = CIBlockElement::GetList(Array(), $arFilterCur); // с помощью метода CIBlockElement::GetList вытаскиваем все значения из нужного элемента
//if ($obCur = $resCur->GetNextElement()){; // переходим к след элементу, если такой есть
//    $arFieldsCur = $obCur->GetFields(); // поля элемента
//    $arPropsCur = $obCur->GetProperties(); // свойства элемента
//
//    $updateCheck = 0;
//    debug($arFieldsCur);
//}

//$arEls = Array();
//$arSelect = Array("ID");
//$arFilter = Array("IBLOCK_ID" => $iblockID);
//$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);
//while ($ob = $res->GetNext()) {
//    $arEls[] = $ob;
//}
//
//foreach ($arEls as $el) {
//    if (CIBlockElement::Delete($el["ID"])) {
//        echo "OK delete " . $el["ID"];
//    } else {
//        echo "ERROR delete " . $el["ID"];
//    }
//
//}

//$arFilterType = Array("IBLOCK_ID"=>153);
//$arSelectType = Array("ID", "NAME",);
//$arFieldsType = array();
//$resType = CIBlockElement::GetList(Array(), $arFilterType, $arSelectType); // с помощью метода CIBlockElement::GetList вытаскиваем все значения из нужного элемента
//while ($obType = $resType->GetNextElement()){; // переходим к след элементу, если такой есть
//    $arFieldsType[] = $obType->GetFields(); // поля элемента
//}
//debug($arFieldsType);
//$rsParentSection = CIBlockSection::GetByID(2210);
//if ($arParentSection = $rsParentSection->GetNext()) {
//    $arFilter = array('IBLOCK_ID' => $arParentSection['IBLOCK_ID'], '>LEFT_MARGIN' => $arParentSection['LEFT_MARGIN'], '<RIGHT_MARGIN' => $arParentSection['RIGHT_MARGIN'], '>DEPTH_LEVEL' => $arParentSection['DEPTH_LEVEL']); // выберет потомков без учета активности
//    $rsSect = CIBlockSection::GetList(array('left_margin' => 'asc'), $arFilter);
//
//    while ($arSect = $rsSect->GetNext()) {
//        // получаем подразделы
//        $sections[] = $arSect['ID'];
//    }
//}
//
//$arSelect = array();
//$arFilter = array("SECTION_ID" => $sections, "ACTIVE" => "Y");
//$res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
//$i = 0;
//while ($ob = $res->GetNextElement()) {
//    if ($ob->GetFields()['IBLOCK_ID'] == 61) {
//        $arFieldsFact[] = $ob->GetFields();
//    }
//}
//
//debug($arFieldsFact[1]);
?>
<style>
    .fields-select__item {
        display: flex;
        border-bottom: 1px solid gray;
        padding: 5px;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .form-error,
    .form-message {
        /*max-width: 100%;
        padding: 15px;
        background-color: #CD5C5C;
        
        border: 2px solid gray
        margin-bottom: 20px;*/
        display: none;
        position: relative;
        z-index: 0;
    }

    .form-error>div{
        margin-bottom: 10px;
        
    }

    .loading-background {
        display: none;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        overflow: hidden;
        background-color: rgba(0, 0, 0, .8);
        z-index: 1;
    }

    .loading-background div{

    }
</style>
<div class="loading-background">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Загрузка...</span>
        </div>
    </div> 
<main class="equip_page">
    <div class="ib-import container">
        <h3>Импорт оборудования</h3>
        <div class="form-message alert alert-success"></div>
        <div class="form-error alert alert-danger"></div>
        <form id="form-import" enctype="multipart/form-data" action="./import.php" method="POST"></form> 
    </div>
    
    
<script>
    const dataFile = <?=json_encode($file)?>;
    const properties = <?=json_encode($properties)?>;
    const iblockID = <?=$iblockID?>;
    const form = document.getElementById('form-import')
    const size = 5; //размер подмассива
    // console.log(dataFile)

    // let formData = new FormData
    // formData.append('iblockID', iblockID);
    // let arrJson = JSON.stringify(dataFile)
    // formData.append('file', arrJson);
    // formData.append('codeParent', "SOSTAVNOE_OBORUDOVANIE");
    // for (let i = 0; i < dataFile[0].length; i++) {
    //     formData.append('fields[]', this[i].value)
    // }
    // console.log(formData);

    document.addEventListener('DOMContentLoaded', () => {
        createFormImport(form)
        form.addEventListener('submit', formSubmit)
    })

    function ajaxImport (j, subarray, records, recordsDone, recordsError, loadingData, totalLength, fieldsForm) {
        records = Number(records);
        recordsDone = Number(recordsDone);
        recordsError = Number(recordsError);
        loadingData = Number(loadingData);
        totalLength = Number(totalLength);
        j = Number(j);
        let formData = new FormData
        formData.append('iblockID', iblockID);
        let arrJson = JSON.stringify(subarray[j])
        formData.append('file', arrJson);
        formData.append('codeParent', "SOSTAVNOE_OBORUDOVANIE");
        for (let i = 0; i < dataFile[0].length; i++) {
            formData.append('fields[]', fieldsForm[i])
        }
        const loading = document.querySelector('.loading-background')
        // console.log(formData)
        $.ajax({
            url: './functions/ajax.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (data) {
                // console.log(data)
                loading.style.display = 'none'
                if (data.status) {
                    const errorBlock = document.querySelector('.form-error')
                    const formError = document.querySelector('.form-error')
                    if (Object.keys(data.error).length !== 0) {

                        // errorBlock.textContent = ''
                        errorBlock.style.display = 'block'
                        if (Object.keys(data.error).includes('error1') || Object.keys(data.error).includes('error2')) {
                            for (let error in data.error) {
                                errorBlock.insertAdjacentHTML('beforeend', `<div>Предупреждение: ${data.error[error]}</div>`)
                            }
                        } else {

                            const blockMessage = document.querySelector('.form-message')
                            blockMessage.textContent = ''
                            form.style.display = "none"
                            recordsDone += data.add;
                            loadingData++;
                            blockMessage.insertAdjacentHTML('beforeend', `Добавлено: ${recordsDone} из ${records} <br>`)
                            if (loadingData !=  totalLength) {
                                blockMessage.insertAdjacentHTML('beforeend', `Ожидайте...`)
                            } else {
                                blockMessage.insertAdjacentHTML('beforeend', `Готово!`)
                            }

                            for (let error in data.error) {
                                recordsError = Number(error) + Number(size) * Number(j) + 1;
                                errorBlock.insertAdjacentHTML('beforeend', `<div>Предупреждение: ${data.error[error]} Строка: ${recordsError}</div>`)
                            }
                            // recordsDone += data.add;
                            // loadingData++;
                            // errorBlock.insertAdjacentHTML('beforeend', `<div>Добавлено: ${recordsDone} из ${records}</div>`)
                            j++;
                            if (loadingData != totalLength) {
                                ajaxImport(j, subarray, records, recordsDone, recordsError, loadingData, totalLength, fieldsForm);
                            } else {
                                return true;
                            }
                        }
                        window.scrollTo(0, 0)
                    } else {
                        //errorBlock.style.display = 'none'
                        const blockMessage = document.querySelector('.form-message')
                        blockMessage.textContent = ''
                        form.style.display = "none"
                        recordsDone += data.add;
                        loadingData++;
                        blockMessage.insertAdjacentHTML('beforeend', `Добавлено: ${recordsDone} из ${records} <br>`)
                        if (loadingData !=  totalLength) {
                            blockMessage.insertAdjacentHTML('beforeend', `Ожидайте...`)
                        } else {
                            blockMessage.insertAdjacentHTML('beforeend', `Готово!`)
                        }
                        blockMessage.style.display = 'block'
                        j++;
                        if (loadingData != totalLength) {
                            ajaxImport(j, subarray, records, recordsDone, recordsError, loadingData, totalLength, fieldsForm);
                        } else {
                            return true;
                        }
                    }
                } else {
                    const formError = document.querySelector('.form-error')
                    formError.textContent = data.message
                    formError.style.opacity = 1
                }

            }
        })
    }

    function formSubmit (event) {
        event.preventDefault()

        let subarray = []; //массив в который будет выведен результат.
        for (let i = 0; i < Math.ceil(dataFile.length/size); i++){
            subarray[i] = dataFile.slice((i*size), (i*size) + size);
        }
        for (let i = 1; i < subarray.length; i++) {
            subarray[i].unshift(dataFile[0]);
        }

        // const errorBlock = document.querySelector('.form-error')
        // errorBlock.textContent = ''
        // errorBlock.style.display = 'block'

        let records = dataFile.length - 1;
        let recordsDone = 0;
        let recordsError = 0;
        let loadingData = 0;
        let totalLength = subarray.length;

        let fieldsForm = [];

        for (let i = 0; i < dataFile[0].length; i++) {
            fieldsForm[i] = this[i].value;
        }

        const loading = document.querySelector('.loading-background')
        loading.style.display = 'flex'
        ajaxImport(0, subarray, records, recordsDone, recordsError, loadingData, totalLength, fieldsForm);

        // for (let j = 0; j < subarray.length; j++) {
        //     let formData = new FormData
        //     formData.append('iblockID', iblockID);
        //     let arrJson = JSON.stringify(subarray[j])
        //     formData.append('file', arrJson);
        //     formData.append('codeParent', "SOSTAVNOE_OBORUDOVANIE");
        //     for (let i = 0; i < dataFile[0].length; i++) {
        //         formData.append('fields[]', this[i].value)
        //     }
        //     const loading = document.querySelector('.loading-background')
        //     // console.log(formData)
        //     loading.style.display = 'flex'
        //     $.ajax({
        //         async: false,
        //         url: './functions/ajax.php',
        //         data: formData,
        //         dataType: 'json',
        //         processData: false,
        //         contentType: false,
        //         type: 'POST',
        //         success: function (data) {
        //             console.log(data)
        //             loading.style.display = 'none'
        //             if (data.status) {
        //                 const errorBlock = document.querySelector('.form-error')
        //                 const formError = document.querySelector('.form-error')
        //                 if (Object.keys(data.error).length !== 0) {
        //
        //                     errorBlock.textContent = ''
        //                     errorBlock.style.display = 'block'
        //                     if (Object.keys(data.error).includes('error1') || Object.keys(data.error).includes('error2')) {
        //                         for (let error in data.error) {
        //                             errorBlock.insertAdjacentHTML('beforeend', `<div>Предупреждение: ${data.error[error]}</div>`)
        //                         }
        //                     } else {
        //                         for (let error in data.error) {
        //                             recordsError += error;
        //                             errorBlock.insertAdjacentHTML('beforeend', `<div>Предупреждение: ${data.error[error]} Строка: ${recordsError}</div>`)
        //                         }
        //                         recordsDone += data.add;
        //                         loadingData++;
        //                         errorBlock.insertAdjacentHTML('beforeend', `<div>Добавлено: ${recordsDone} из ${records}</div>`)
        //                     }
        //                     window.scrollTo(0, 0)
        //                 } else {
        //                     errorBlock.style.display = 'none'
        //                     const blockMessage = document.querySelector('.form-message')
        //                     blockMessage.textContent = ''
        //                     form.style.display = "none"
        //                     recordsDone += data.add;
        //                     loadingData++;
        //                     blockMessage.insertAdjacentHTML('beforeend', `Добавлено: ${recordsDone} из ${records} <br>`)
        //                     if (loadingData !=  subarray.length) {
        //                         blockMessage.insertAdjacentHTML('beforeend', `Ожидайте...`)
        //                     } else {
        //                         blockMessage.insertAdjacentHTML('beforeend', `Готово!`)
        //                     }
        //                     blockMessage.style.display = 'block'
        //
        //                 }
        //             } else {
        //                 const formError = document.querySelector('.form-error')
        //                 formError.textContent = data.message
        //                 formError.style.opacity = 1
        //             }
        //
        //         }
        //     })
        // }
    }

    function createFormImport (form) {
        const colName = dataFile[0]
        // console.log(colName)

        // let size = 500; //размер подмассива
        // let subarray = []; //массив в который будет выведен результат.
        // for (let i = 0; i < Math.ceil(dataFile.length/size); i++){
        //     subarray[i] = dataFile.slice((i*size), (i*size) + size);
        // }
        // for (let i = 1; i < subarray.length; i++) {
        //     subarray[i].unshift(dataFile[0]);
        // }
        // console.log(subarray);

        form.innerHTML = ''
        form.insertAdjacentHTML('afterbegin', '<div class="form__fields-select"></div')
        const blockFieldsSelect = form.querySelector('.form__fields-select')
        i = 0

        colName.forEach(el => {
            let match = 0;
            let element = 0;
            let matchElement = 0;
            fieldName = el;
            properties.forEach(property => {
                let counter = 0;
                let slice = property.NAME.toLowerCase().split(' ');
                for (let l = 0; l < slice.length; l++) {
                    if (fieldName.toLowerCase().indexOf(slice[l]) != -1) {
                        counter++;
                    }
                }
                if (counter > match) {
                    match = counter;
                    matchElement = element;
                }
                element++;
            })

            let options = `
                <option value="none">-</option>
            `

            if (fieldName.length > 0) {
                let counterProperty = 0;
                properties.forEach(property => {
                    if (fieldName.toLowerCase().indexOf('наименование') != -1 && fieldName.toLowerCase().indexOf('мол') != -1) {
                        if (property.NAME.toLowerCase() == 'мол') {
                            options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
                        } else {
                            options += `<option value="${property.ID}">${property.NAME}</option></br>`
                        }
                    } else if (fieldName.toLowerCase().indexOf('наименование') != -1 && fieldName.toLowerCase().indexOf('тмц') != -1 && fieldName.toLowerCase().length < 20) {
                        if (property.NAME.toLowerCase() == 'наименование') {
                            options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
                        } else {
                            options += `<option value="${property.ID}">${property.NAME}</option></br>`
                        }
                    } else if (fieldName.toLowerCase().indexOf('вид') != -1 && fieldName.toLowerCase().indexOf('номенклатуры') != -1 && fieldName.toLowerCase().indexOf('наименование') != -1) {
                        if (property.NAME.toLowerCase() == 'тип') {
                            options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
                        } else {
                            options += `<option value="${property.ID}">${property.NAME}</option></br>`
                        }
                    } else if (fieldName.toLowerCase().indexOf('наименование') != -1 && fieldName.toLowerCase().indexOf('подразделение') != -1) {
                        if (property.NAME.toLowerCase() == 'структурное подразделение') {
                            options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
                        } else {
                            options += `<option value="${property.ID}">${property.NAME}</option></br>`
                        }
                    } else if (fieldName.toLowerCase().indexOf('комментарий') != -1) {
                        if (property.NAME.toLowerCase() == 'примечание') {
                            options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
                        } else {
                            options += `<option value="${property.ID}">${property.NAME}</option></br>`
                        }
                    } else if (counterProperty == matchElement) {
                        options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
                    } else {
                        options += `<option value="${property.ID}">${property.NAME}</option></br>`
                    }
                    counterProperty++;
                })
            }

            const selectProperties = `<select id="el-${i}">${options}</select>`
            let style = ''
            if (!el) {
                style = 'display: none';
            }
            let fieldHTML = `
                <div style="${style}" class="fields-select__item">
                    <label from="el-${i}">${i+1+". "+el}</label>
                    ${selectProperties}
                </div>
            `
            if (el) {
                i++
            }
            blockFieldsSelect.insertAdjacentHTML('beforeend', fieldHTML)
        })

        // colName.forEach(el => {
        //     fieldCode = el.substr(0, el.indexOf('('))
        //     fieldName = el
        //     console.log(fieldName)
        //     let options = `
        //         <option value="none">-</option>
        //     `
        //     // if (fieldCode.toLowerCase() === 'NAME'.toLowerCase()) {
        //     //     options += `<option selected value="name">Название</option></br>`
        //     // } else {
        //     //     options += `<option value="name">Название</option></br>`
        //     // }
        //     //
        //     // if (fieldCode.toLowerCase() === 'XML_ID'.toLowerCase()) {
        //     //     options += `<option selected value="xml_id">Внешний код</option></br>`
        //     // } else {
        //     //     options += `<option value="xml_id">Внешний код</option></br>`
        //     // }
        //
        //     properties.forEach(property => {
        //         if (fieldCode.toLowerCase() === property.CODE.toLowerCase()) {
        //             options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
        //         } else if (fieldName.toLowerCase() === property.NAME.toLowerCase()) {
        //             options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
        //         } else {
        //             options += `<option value="${property.ID}">${property.NAME}</option></br>`
        //         }
        //
        //     })
        //     const selectProperties = `<select id="el-${i}">${options}</select>`
        //     let style = ''
        //     if (!el) {
        //         style = 'display: none';
        //     }
        //     let fieldHTML = `
        //         <div style="${style}" class="fields-select__item">
        //             <label from="el-${i}">${i+1+". "+el}</label>
        //             ${selectProperties}
        //         </div>
        //     `
        //     if (el) {
        //         i++
        //     }
        //     blockFieldsSelect.insertAdjacentHTML('beforeend', fieldHTML)
        // })

        form.insertAdjacentHTML('beforeend', '<input type="submit" value="Импортировать">')
    }
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>