import '../css/app.scss';

import { Dropdown } from 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    enableDropdown
    //new App();
})

const enableDropdown = () => {
    const dropdownElementList = document.querySelectorAll('.dropdown-toggle')
    const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl))

}

// import './bootstrap';
// import '../css/app.scss';




// require('@fortawesome/fontawesome-free/css/all.min.css');


// import { Dropdown } from 'bootstrap';

// document.addEventListener('DOMContentLoaded', () => {
//     enableDropdown
//     //new App();
// })

// const enableDropdown = () => {
//     const dropdownElementList = document.querySelectorAll('.dropdown-toggle')
//     const dropdownList = [...dropdownElementList].map(dropdownToggleEl => new bootstrap.Dropdown(dropdownToggleEl))

// }

// enableDropdown() {
//     const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
//     dropdownElementList.map(function (dropdownToggleEl) {
//         return new Dropdown(dropdownToggleEl);
//     });}



