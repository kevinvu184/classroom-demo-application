var markRegex=/^(?:[1-9]|0[1-9]|10)$/;
function markValidation(){
    var validation = true;
    var inputs=document.getElementsByTagName('input');
    for(let index=0;index<inputs.length;++index){
        if(!markRegex.test(inputs[index].value)){
            validation=false;
            document.getElementById(inputs[index].name).innerHTML='<div>Please input a number between 1-10</div>';
            document.getElementById(inputs[index].name).style.color='red';
        }
    }
    return false;
}