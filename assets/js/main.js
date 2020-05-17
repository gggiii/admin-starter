//SIDEBAR ANIMATION
let nav = document.querySelector('nav')
if(nav ){
    nav.addEventListener('mouseenter', e=>{
        document.querySelector('body').classList.remove('navHidden')
    })
    nav.addEventListener('mouseleave', e=>{
        document.querySelector('body').classList.add('navHidden')
    })
}



//HEADER DROPDOWN
if(document.querySelector('.headerDropdownToggle')){

    document.querySelector('.headerDropdownToggle').addEventListener('click',e=>{
        document.querySelector('.headerDropdown').classList.toggle('headerDropdownHidden')
    })
}
//HIDE DROPDOWN ON MAIN CLICK
if(document.querySelector('main')){
    
    document.querySelector('main').addEventListener('click',e=>{
        document.querySelector('.headerDropdown').classList.add('headerDropdownHidden')
    })
}

//INPUT COUNTER
if(document.querySelectorAll('[maxlength]').length > 0){
    let a = document.querySelectorAll('[maxlength]');
    for (let i = 0; i < a.length; i++) {
        const element = a[i];
        let max = parseInt(element.getAttribute('maxlength'))
        element.addEventListener('input',e=>{
            
            element.previousElementSibling.children[0].innerHTML = element.value.length+'/'+max
             
        })
        
    }
}
//TAGS RESIZE
function tagList(){
    
    let tags = document.querySelectorAll('.tag');
    for (let i = 0; i < tags.length; i++) {
        const element = tags[i];
        element.addEventListener('input',()=>{
            element.setAttribute('size',element.value.length+2)
            if(element.value == '' && element.parentElement.querySelectorAll('.tag').length > 1){
                element.remove()
            }
            if(!element.nextElementSibling && element.value != ''){
                let a = element.cloneNode(true)
                element.parentElement.appendChild(a)
                a.value = ''
                tagList()
            }
        })
    }
}
tagList()


//EDIT FILE OPEN FILE BROWSER
let browserButtons = document.querySelectorAll('.openFiles')
for (let i = 0; i < browserButtons.length; i++) {
    const element = browserButtons[i];
    element.onclick = e=>{
        e.preventDefault();
        let returnto = element.getAttribute('data-returnto')
        window.open('selectBrowser.php?p=articles&returnto='+returnto,'_blank','width=400,height=800');
        
    }
    
}