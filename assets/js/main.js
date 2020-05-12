//SIDEBAR ANIMATION
let nav = document.querySelector('nav')
nav.addEventListener('mouseenter', e=>{
    document.querySelector('body').classList.remove('navHidden')
})
nav.addEventListener('mouseleave', e=>{
    document.querySelector('body').classList.add('navHidden')
})



//HEADER DROPDOWN
document.querySelector('.headerDropdownToggle').addEventListener('click',e=>{
    document.querySelector('.headerDropdown').classList.toggle('headerDropdownHidden')
})
//HIDE DROPDOWN ON MAIN CLICK
document.querySelector('main').addEventListener('click',e=>{
    document.querySelector('.headerDropdown').classList.add('headerDropdownHidden')
})