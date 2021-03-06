///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//__________________________________________________________ FLOATING LABEL TEXT INPUTS  _________________________________________________________
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//For floating labels: User clicks on text input and label floats up.

const labelMod = "input__label--selected";
const labelModFloating = "input__label--floating"

//Each class contains a label & a text input. The input listens for user inputs and styles the label.
//________________________________________________________________ Pair Class
var Pair = function(label, input) {
	this.label = label;
	this.input = input;

	//add floating modifier
	if (!this.label.classList.contains(labelModFloating)) this.label.classList.add(labelModFloating);

  if (this.input.value !== "") if (!this.label.classList.contains(labelMod)) this.label.classList.add(labelMod);

  this.input.addEventListener('input', (event) => {
    this.label.classList.add(labelMod);
    //console.log("Focus on: " + this.input.id + ", with label: " + this.label.innerHTML);
    //console.log("text:" + event.target.value);

    //Change label style on input
    if (event.target.value === ""){
      if (this.label.classList.contains(labelMod)) this.label.classList.remove(labelMod);
    } else {
      if (!this.label.classList.contains(labelMod)) this.label.classList.add(labelMod);
    }
  });
};

//Find all cells
var cells = [...document.querySelectorAll(".form__cell")];

//Create pair classes for each cell that contains exactly one label & one input
for (let i = 0; i < cells.length; i++) {
	
	let cell = cells[i];
	let labels = [...cell.querySelectorAll(".input__label")];
	let inputs = [...cell.querySelectorAll(".input__text")];

	if (labels.length === 1 && inputs.length === 1){
		new Pair(labels[0], inputs[0]);
	}
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//__________________________________________ DISABLE SUBMIT ON REGISTER UNTIL ALL FIELDS COMPLETE  _______________________________________________
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var inputs = document.forms["form_register"].getElementsByTagName("input");
var selects = document.forms["form_register"].getElementsByTagName("select");
var submitBtn = document.getElementById("submit_register");

var fieldsComplete = [];
var fields = 7;     
for (let i = 0; i < fields; i++) {
  fieldsComplete.push(false);
}

console.log(inputs);
console.log(selects);

inputs[0].addEventListener("input", (e) => { fieldsComplete[0] = !(e.target.value === ""); updateButton(); });
inputs[1].addEventListener("input", (e) => { fieldsComplete[1] = !(e.target.value === ""); updateButton(); });
inputs[2].addEventListener("input", (e) => { fieldsComplete[2] = !(e.target.value === ""); updateButton(); });
inputs[3].addEventListener("input", (e) => { fieldsComplete[3] = !(e.target.value === ""); updateButton(); });

function updateButton() {
  let allFieldsComplete = true;
  for (let i = 0; i < fields; i++) {
    if (!fieldsComplete[i]) allFieldsComplete = false;
  }
  if (allFieldsComplete){
    if (submitBtn.classList.contains("input__submit--disabled")) {
      submitBtn.classList.remove("input__submit--disabled");
      submitBtn.removeAttribute("title");
      submitBtn.disabled = false;
    }
  } else {
    if (!submitBtn.classList.contains("input__submit--disabled")) {
      submitBtn.classList.add("input__submit--disabled");
      submitBtn.setAttribute('title', "Complete all fields to register");
      submitBtn.disabled = true;
    }
  }
}



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//______________________________________________ DISABLE SUBMIT ON LOGIN UNTIL ALL FIELDS COMPLETE _______________________________________________
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function login(){
  var inputs = document.forms["form_login"].getElementsByTagName("input");
  var submitBtn = document.getElementById("submit_login");

  var fieldsComplete = [];
  var fields = 2;     
  for (let i = 0; i < fields; i++) {
    fieldsComplete.push(false);
  }


  inputs[0].addEventListener("input", (e) => { fieldsComplete[0] = !(e.target.value === ""); updateButton(); });
  inputs[1].addEventListener("input", (e) => { fieldsComplete[1] = !(e.target.value === ""); updateButton(); });

  function updateButton() {
    let allFieldsComplete = true;
    for (let i = 0; i < fields; i++) {
      if (!fieldsComplete[i]) allFieldsComplete = false;
    }
    if (allFieldsComplete){
      if (submitBtn.classList.contains("input__submit--disabled")) {
        submitBtn.classList.remove("input__submit--disabled");
        submitBtn.removeAttribute("title");
        submitBtn.disabled = false;
      }
    } else {
      if (!submitBtn.classList.contains("input__submit--disabled")) {
        submitBtn.classList.add("input__submit--disabled");
        submitBtn.setAttribute('title', "Complete all fields to sign in");
        submitBtn.disabled = true;
      }
    }
  }

}
login();



///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//______________________________________________ DISABLE SUBMIT ON ACCOUNT UNTIL ALL FIELDS COMPLETE _______________________________________________
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function account(){
  if (document.forms["form_account"] !== undefined){
    var inputs = document.forms["form_account"].getElementsByTagName("input");
    var submitBtn = document.getElementById("submit_account");

    var fieldsComplete = [];
    var fields = 2;     
    for (let i = 0; i < fields; i++) {
      fieldsComplete.push(false);
    }

    var fieldsChanged = []; 
    for (let i = 0; i < fields; i++) {
      fieldsChanged.push(false);
    }

    //Use pattern to ensure both old and new password have been entered

    let origEmail = inputs[0].value;
    let origName = inputs[1].value;

    inputs[0].addEventListener("input", (e) => { fieldsChanged[0] = (e.target.value !== origEmail); updateButton(); });
    inputs[1].addEventListener("input", (e) => { fieldsChanged[1] = (e.target.value !== origName); updateButton(); });
    inputs[2].addEventListener("input", (e) => { fieldsComplete[0] = !(e.target.value === ""); updateButton(); });
    inputs[3].addEventListener("input", (e) => { fieldsComplete[1] = !(e.target.value === ""); updateButton(); });

    function updateButton() {
      //Update button, if EITHER name or email has been changed OR both the old and new password have been entered (or all of those)
      let allFieldsComplete = true;
      let nameOrEmailChanged = false;
      for (let i = 0; i < fields; i++) {
        if (!fieldsComplete[i]) allFieldsComplete = false;
      }
      for (let i = 0; i < fields; i++) {
        if (fieldsChanged[i]) nameOrEmailChanged = true;
      }
      if (allFieldsComplete || nameOrEmailChanged){
        if (submitBtn.classList.contains("input__submit--disabled")) {
          submitBtn.classList.remove("input__submit--disabled");
          submitBtn.removeAttribute("title");
          submitBtn.disabled = false;
        }
      } else {
        if (!submitBtn.classList.contains("input__submit--disabled")) {
          submitBtn.classList.add("input__submit--disabled");
          submitBtn.setAttribute('title', "Complete all fields to sign in");
          submitBtn.disabled = true;
        }
      }
    }
  }
}
account();


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//_____________________________________________________________________ STYLIZE DROPDOWNS _________________________________________________________
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/* 
based on: https://css-tricks.com/striking-a-balance-between-native-and-custom-select-elements/

Allows us to stylize the form dropdowns, by replacing them with divs, while maintaining form / noscript accessibility.

Features needed to make the selectCustom work for mouse users.
- Toggle custom select visibility when clicking the "box"
- Update custom select value when clicking in a option
- Navigate through options when using keyboard up/down
- Pressing Enter or Space selects the current hovered option
- Close the select when clicking outside of it
- Sync both selects values when selecting a option. (native or custom)

TODO improve consistency: 
- make left alignment of text in dropdown match other fields.
- once value set, set text color to whiteTransparent (like other fields)

*/

var itemCount = document.getElementsByClassName("js-selectNative").length;
//custom
var dobLabel = document.getElementsByClassName("js-dob__label")[0];
dobLabel.classList.add("input__label--floating");

//console.log(itemCount);


for(var i = 0; i < itemCount; i++) {
  console.log(i);

  const num = i;
  const elSelectNative = document.getElementsByClassName("js-selectNative")[i];
  const elSelectCustom = document.getElementsByClassName("js-selectCustom")[i];
  const elSelectCustomTrigger = document.getElementsByClassName("selectCustom-trigger")[i];
  const elSelectCustomBox = elSelectCustom.children[0];
  const elSelectCustomOpts = elSelectCustom.children[1];
  const customOptsList = Array.from(elSelectCustomOpts.children);
  const optionsCount = customOptsList.length;
  const defaultLabel = elSelectCustomBox.getAttribute("data-value");

  let optionChecked = "";
  let optionHoveredIndex = -1;

  //////////// Label mod
  const elLabel = document.getElementsByClassName("js-selectLabel")[i];
  if (!elLabel.classList.contains(labelModFloating)) elLabel.classList.add(labelModFloating);

  // Toggle custom select visibility when clicking the box
  elSelectCustomBox.addEventListener("click", (e) => {
    const isClosed = !elSelectCustom.classList.contains("isActive");

    if (isClosed) {
      openSelectCustom();
    } else {
      closeSelectCustom();
    }
  });

  function openSelectCustom() {
    elSelectCustom.classList.add("isActive");
    // Remove aria-hidden in case this was opened by a user
    // who uses AT (e.g. Screen Reader) and a mouse at the same time.
    elSelectCustom.setAttribute("aria-hidden", false);

    if (optionChecked) {
      const optionCheckedIndex = customOptsList.findIndex(
        (el) => el.getAttribute("data-value") === optionChecked
      );
      updateCustomSelectHovered(optionCheckedIndex);
    }

    // Add related event listeners
    document.addEventListener("click", watchClickOutside);
    document.addEventListener("keydown", supportKeyboardNavigation);
  }

  function closeSelectCustom() {
    elSelectCustom.classList.remove("isActive");

    elSelectCustom.setAttribute("aria-hidden", true);

    updateCustomSelectHovered(-1);

    // Remove related event listeners
    document.removeEventListener("click", watchClickOutside);
    document.removeEventListener("keydown", supportKeyboardNavigation);
  }

  function updateCustomSelectHovered(newIndex) {
    const prevOption = elSelectCustomOpts.children[optionHoveredIndex];
    const option = elSelectCustomOpts.children[newIndex];

    if (prevOption) {
      prevOption.classList.remove("isHover");
    }
    if (option) {
      option.classList.add("isHover");
    }

    optionHoveredIndex = newIndex;
  }

  ////////////////////////////////////////
  function floatUpLabel() {
    if (!elLabel.classList.contains("input__label--selected"))elLabel.classList.add("input__label--selected");
    if (elSelectCustomTrigger.classList.contains("selectCustom-trigger--inactive")) elSelectCustomTrigger.classList.remove("selectCustom-trigger--inactive");
    dobLabel.classList.add("input__label--selected");
    fieldsComplete[4+num] = true;
    updateButton();
  }

  function updateCustomSelectChecked(value, text) {
    const prevValue = optionChecked;

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    floatUpLabel();

    const elPrevOption = elSelectCustomOpts.querySelector(
      `[data-value="${prevValue}"`
    );
    const elOption = elSelectCustomOpts.querySelector(`[data-value="${value}"`);

    if (elPrevOption) {
      elPrevOption.classList.remove("isActive");
    }

    if (elOption) {
      elOption.classList.add("isActive");
    }

    elSelectCustomBox.textContent = text;
    optionChecked = value;
  }

  function watchClickOutside(e) {
    const didClickedOutside = !elSelectCustom.contains(event.target);
    if (didClickedOutside) {
      closeSelectCustom();
    }
  }

  function supportKeyboardNavigation(e) {
    // press down -> go next
    if (event.keyCode === 40 && optionHoveredIndex < optionsCount - 1) {
      let index = optionHoveredIndex;
      e.preventDefault(); // prevent page scrolling
      updateCustomSelectHovered(optionHoveredIndex + 1);
    }

    // press up -> go previous
    if (event.keyCode === 38 && optionHoveredIndex > 0) {
      e.preventDefault(); // prevent page scrolling
      updateCustomSelectHovered(optionHoveredIndex - 1);
    }

    // press Enter or space -> select the option
    if (event.keyCode === 13 || event.keyCode === 32) {
      e.preventDefault();

      const option = elSelectCustomOpts.children[optionHoveredIndex];
      const value = option && option.getAttribute("data-value");

      if (value) {
        elSelectNative.value = value;
        fieldsComplete[4+num] = true;  //Custom Code
        updateButton(); 
        updateCustomSelectChecked(value, option.textContent);
      }
      closeSelectCustom();
    }

    // press ESC -> close selectCustom
    if (event.keyCode === 27) {
      closeSelectCustom();
    }
  }

  // Update selectCustom value when selectNative is changed.
  elSelectNative.addEventListener("change", (e) => {
    const value = e.target.value;
    const elRespectiveCustomOption = elSelectCustomOpts.querySelectorAll(
      `[data-value="${value}"]`
    )[0];

    updateCustomSelectChecked(value, elRespectiveCustomOption.textContent);
  });

  // Update selectCustom value when an option is clicked or hovered
  customOptsList.forEach(function (elOption, index) {
    elOption.addEventListener("click", (e) => {
      const value = e.target.getAttribute("data-value");

      // Sync native select to have the same value
      elSelectNative.value = value;
      fieldsComplete[4+num] = true; 
      updateButton(); 
      updateCustomSelectChecked(value, e.target.textContent);
      closeSelectCustom();
    });

    elOption.addEventListener("mouseenter", (e) => {
      updateCustomSelectHovered(index);
    });

    // TODO: Toggle these event listeners based on selectCustom visibility
  });

}

function echoFunction() {
  console.log("echo");
}