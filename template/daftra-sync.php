<?php 
$html = '<style>
@import url("https://fonts.googleapis.com/css2?family=Mulish:wght@400;600;700&display=swap");
:root {
  --clr-neutral-300: hsl(232, 41%, 97%);
  --clr-neutral-400: hsl(98, 0%, 50%);
  --clr-neutral-900: #020203;
  --clr-accent: #8dc63f;
  /* font weight */
  --fw-400: 400;
  --fw-600: 600;
  --fw-700: 700;
  /* Type */
  --ff-primary: "Mulish", sans-serif;
}
.main,
.container {
  margin: 0 auto;
  font-family : var(--ff-primary);
}

.sync {
  background-color: #fff;
  padding: 2.125em 1.25em;
}

.sync__title {
    font-size: 1.75rem !important;
    margin: 1em !important;
    font-weight: bold !important;
    color: #f58220;

}

.sync-grid {
  display: grid;
  margin-top: 40px;
  text-align: center;
}



label {
    font-size: 1rem;
    white-space: nowrap;
    font-weight: 700;
}

p {
  grid-column: 2/-2;
}

.card {
  padding: 0 12px;
  border : none !important;
  box-shadow: none !important;
}

.card.active {
  background-color: var(--clr-neutral-300);
  height: 88px;
  width: 100%;
}

.sync__form {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-top: 50px;
  justify-content: center;
}

.sync__btn {
  border: none;
  outline: none;
  background: transparent;
  background-color: var(--clr-accent);
  padding: 20px 30px;
  color: white;
  border-radius: 3px;
  cursor: pointer;
}
input[type=checkbox], input[type=radio] {
    height: 1.5625rem;
    width: 1.5625rem;
}
input[type=checkbox]:checked:before {
    width: 1.875rem;
    height: 1.875rem;
    margin: -0.1875rem -0.3125rem;
}
@media (min-width: 550px) {
  .sync-grid {
    grid-auto-flow: column;
    grid-template-columns: repeat(3, 1fr);
  }

  .sync {
    padding: 4.125em 2.25em;
  }

  .sync__form {
    flex-direction: row;
    gap: 10px;
  }

  input[type=email] {
    flex: 0 0 480px;
  }
}
header {
    text-align: center;
    margin-bottom: 33px;
}
div#postbox-container-1 {
    display: none;
}
.sync__loader {
    text-align: center;
    display: none ;
}
.sync__msg,
.sync__msg_error {
    text-align: center;
    padding: 10px;
    max-width: 52%;
    margin: 0 auto;
    display: none ;
}
.sync__msg{
    background: #8bc34a59;
}
.sync__msg_error{
    background: #fb130245;
}

</style>';
$html .= '<form id="daftra_sync_form" action="" method="post">
        <main class="main">
            <div class="container sync flow">
            <header>  
              <img src="https://www.daftra.com/themed/multi_language/images/daftra-ar.svg" alt="دفترة">
              <h2 class="sync__title">Sync Data Between WordPress and Daftra</h2>
            </header>
            <div class="sync__loader">
            <svg version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="40px" height="40px" viewBox="0 0 40 40" enable-background="new 0 0 40 40" xml:space="preserve">
           <path opacity="0.2" fill="#000" d="M20.201,5.169c-8.254,0-14.946,6.692-14.946,14.946c0,8.255,6.692,14.946,14.946,14.946
             s14.946-6.691,14.946-14.946C35.146,11.861,28.455,5.169,20.201,5.169z M20.201,31.749c-6.425,0-11.634-5.208-11.634-11.634
             c0-6.425,5.209-11.634,11.634-11.634c6.425,0,11.633,5.209,11.633,11.634C31.834,26.541,26.626,31.749,20.201,31.749z"/>
           <path fill="#000" d="M26.013,10.047l1.654-2.866c-2.198-1.272-4.743-2.012-7.466-2.012h0v3.312h0
             C22.32,8.481,24.301,9.057,26.013,10.047z">
             <animateTransform attributeType="xml"
               attributeName="transform"
               type="rotate"
               from="0 20 20"
               to="360 20 20"
               dur="0.5s"
               repeatCount="indefinite"/>
             </path>
           </svg>
            </div>
            <div class="sync__msg"></div>
            <div class="sync__msg_error"></div>
            <div class="sync-grid">
                <div class="card__group">
                    <div class="card">
                     <input class="custom_" type="checkbox" id="check1" name="sync_users">
                     <label for="check1">Sync All Users</label>
                    </div>
                </div>
            <div class="card__group">
                <div class="card">
                <input class="custom_" type="checkbox" id="check2" name="sync_products">
                <label for="check2">Sync All Products</label>
                </div>
            </div>
            <div class="card__group">
                <div class="card">
                    <input class="custom_" type="checkbox" id="check3" name="sync_orders">
                    <label for="check3">Sync All Orders</label>
                </div>
            </div>
        </div>
                <div class="sync__form">
                    <input type="submit" class="sync__btn" value="Sync Now" />
                </div>
            
            </div>
        </main></form>';

return $html;