function toggleNav() {
    let nav = document.querySelector('#myTopNav');
    if (nav.className === "topnav") {
        nav.className += " responsive";    
    } else {
        nav.className = "topnav";
    }
}