<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
CJSCore::Init(array("ajax"));
CJSCore::Init(array("jquery"));
$APPLICATION->SetTitle("Техническая информация"); 
$APPLICATION->AddChainItem('Техническая информация', "");
$maxID = lastXMLID();
$iblockID = 85;

function searchPropertyValue($propertyID, $value, $IBLOCK_ID) {
    $propertyDB = CIBlockProperty::GetByID($propertyID);
    if($property = $propertyDB->GetNext()) {
        $arProperty = $property;
    }

    if ($arProperty["PROPERTY_TYPE"] == 'L') {
        $propertyEnums = CIBlockPropertyEnum::GetList(Array("DEF"=>"DESC", "SORT"=>"ASC"), Array("IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>$arProperty['CODE']));
        while($enumFields = $propertyEnums->GetNext()) {
            $enumValue = mb_strtolower (trim($enumFields['VALUE']));
            $value = mb_strtolower (trim($value));
            if (!strcmp($enumValue, $value)) {
                $result = $enumFields['ID'];
            }
        }
        
    } else {
        $result = $value;
    }
    
    return $result;
}

?>

<style>
    

    .form-error {
        opacity: 0;
        color: red;
    }


</style>

<main class="equip_page">
        <div class="container lk"> 
            <div class="row">
                <div>
                    <span>Последний ID: <?=$maxID?></span>
                </div>
            </div>
        </div>
        <div class="ib-import container">
            <h3>Импорт оборудования</h3>
            <form id="form-import" enctype="multipart/form-data" action="./import.php" method="POST">
                <div class="ib-import__file_add form__item">
                    <label for="file-csv-add">Выберите файл формата csv: </label>
                    <input id="file-csv-add" name="file" size="20" type="file" required>
                </div>
                <input type="submit" value="Отправить">
            </form> 

            <div class="form-error">error</div>
        </div>
<script>
   
    document.addEventListener('DOMContentLoaded', () => {
        const inputFile = document.querySelector('input[type="file"]')
        const inputSubmit = document.querySelector('input[type="submit"]')
        const error = document.querySelector('.form-error')

        inputFile.addEventListener('change', (event) => {
            let formatFile = inputFile.files[0].name.split(".").splice(-1,1)[0]

            if (formatFile !== 'csv') {
                addErrorFile(error, inputSubmit, "Неверный формат файла")
            } else {
                removeErrorFile(error, inputSubmit)
            }
        })
        
    })

    function addErrorFile(error, btn, message) {
        error.textContent = message
        error.style.opacity = 1
        btn.setAttribute('disabled', 'disabled')
    }

    function removeErrorFile(error, btn) {
        error.style.opacity = 0
        btn.removeAttribute("disabled")
    }

    /*
     /const iblockID = <?=$iblockID?>;
    const form = document.getElementById('form-import')
    let dataFile 
    function formSubmit (event) {
        event.preventDefault()
        let formData = new FormData
        formData.append('iblockID', iblockID);

        if (event.submitter.value === "Отправить") {
            let inputFile = $("#file-csv-add");
            formData.append('status', "fileProcessing");
            formData.append('file', inputFile.prop('files')[0]);
            console.log(formData)
            $.ajax({
                url: './functions/functions.php',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                type: 'POST',
                success: function (data) {
                    if (data.status) {
                        dataFile = data
                        createFormImport(data)
                    } else {
                        const formError = document.querySelector('.form-error')
                        formError.textContent = data.message
                        formError.style.opacity = 1
                    }
                    
                }
            });
        }

        if (event.submitter.value === "Импортировать") {
            formData.append('status', "import");
            formData.append('file[]', dataFile.file);
            for (let i = 0; i < dataFile.file[0].length; i++) {
                formData.append(`select-${i}`, form[i].value);
            }
            console.log(formData)
        }
        
    }

    function createFormImport (data) {
        const colName = data.file[0]
        const properties = data.fields;

        let options = `
            <option value="none">-</option>
            <option value="name-equipment">Название</option>
            <option value="xml_id-equipment">Внешний код</option>
        `

        properties.forEach(el => {
            options += `<option value="${el.ID}">${el.NAME}</option></br>`
        })
        
        form.innerHTML = ''
        form.insertAdjacentHTML('afterbegin', '<div class="form__fields-select"></div')
        const blockFieldsSelect = form.querySelector('.form__fields-select')
        i = 0
        colName.forEach(el => {
            const selectProperties = `<select id="el-${i}">${options}</select>`
            let fieldHTML = `
                <div class="fields-select__item">
                    <label from="el-${i}">${i+1+". "+el}</label>
                    ${selectProperties}
                </div>
            `
            i++
            blockFieldsSelect.insertAdjacentHTML('beforeend', fieldHTML)
        })

        form.insertAdjacentHTML('beforeend', '<input type="submit" value="Импортировать">')
    }

    function createFormFile() {
        const formHTML = `
            <div class="ib-import__file_add form__item">
                <label for="file-csv-add">Выберите файл формата csv: </label>
                <input id="file-csv-add" name="file" size="20" type="file">
            </div>
            <input type="submit" value="Отправить">
        `
        form.insertAdjacentHTML('afterbegin', formHTML)
    }*/
    
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>