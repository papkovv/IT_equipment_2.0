<? require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); 

require './functions/functions.php';

$filePath = $_FILES['file']['tmp_name'];
if (empty($_FILES) || empty($filePath)) {
    header('Location: index.php');
}

$iblockID = 85;
$file = kama_parse_csv_file($filePath);
$properties = propertiesList($iblockID);

//debug($elementID);
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
    console.log(dataFile)

    document.addEventListener('DOMContentLoaded', () => {
        createFormImport(form)
        form.addEventListener('submit', formSubmit)
    })

    function formSubmit (event) {
        event.preventDefault()
        let formData = new FormData
        formData.append('iblockID', iblockID);
        let arrJson = JSON.stringify(dataFile)
        formData.append('file', arrJson);
        formData.append('codeParent', "SOSTAVNOE_OBORUDOVANIE");
        for (let i = 0; i < dataFile[0].length; i++) {
            formData.append('fields[]', this[i].value)
        }
        const loading = document.querySelector('.loading-background')
        loading.style.display = 'flex'
        $.ajax({
            url: './functions/ajax.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            type: 'POST',
            success: function (data) {
                console.log(data)
                loading.style.display = 'none'
                if (data.status) {
                    const errorBlock = document.querySelector('.form-error')
                    const formError = document.querySelector('.form-error')
                    if (Object.keys(data.error).length !== 0) {
                        
                        errorBlock.textContent = ''
                        errorBlock.style.display = 'block'
                        if (Object.keys(data.error).includes('error1') || Object.keys(data.error).includes('error2')) {
                            for (let error in data.error) {
                                errorBlock.insertAdjacentHTML('beforeend', `<div>ОШИБКА: ${data.error[error]}</div>`)
                            }
                        } else {
                            for (let error in data.error) {
                                errorBlock.insertAdjacentHTML('beforeend', `<div>ОШИБКА: ${data.error[error]}Строка: ${error}</div>`)
                            }
                            errorBlock.insertAdjacentHTML('beforeend', `<div>Добавлено: ${data.add} из ${data.all}</div>`)
                        }
                        window.scrollTo(0, 0)
                    } else {
                        errorBlock.style.display = 'none'
                        const blockMessage = document.querySelector('.form-message')
                        blockMessage.textContent = ''
                        form.style.display = "none"
                        blockMessage.insertAdjacentHTML('beforeend', `Добавлено: ${data.add} из ${data.all}`)
                        blockMessage.style.display = 'block'

                    }
                } else {
                    const formError = document.querySelector('.form-error')
                    formError.textContent = data.message
                    formError.style.opacity = 1
                }
                
            }
        })  
    }

    function createFormImport (form) {
        const colName = dataFile[0]

        
        //console.log(options)
        form.innerHTML = ''
        form.insertAdjacentHTML('afterbegin', '<div class="form__fields-select"></div')
        const blockFieldsSelect = form.querySelector('.form__fields-select')
        i = 0
        colName.forEach(el => {
            let options = `
                <option value="none">-</option>
                <option value="name-equipment">Название</option>
                <option value="xml_id-equipment">Внешний код</option>
            `

            properties.forEach(property => {
                if (el.toLowerCase() === property.NAME.toLowerCase()) {
                    options += `<option selected value="${property.ID}">${property.NAME}</option></br>`
                } else {
                    options += `<option value="${property.ID}">${property.NAME}</option></br>`
                }
                
            })
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
</script>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>