    var faqTrigger = document.getElementById('basics');
    // the trigger for the content to display

    var faqState = {};
    var currentIndex = [];


    /**
     * 
     * @param array ourarray - Pass an array that needs to be checked if it has values before doing somthing with it (returns true when there are no values within the array).  
     */
    function isArrayUndefined(ourarray){
        if(ourarray === undefined){
            ourarray = true;
        }
        else{
            ourarray = false;
        }
        return ourarray;
    }

    /**
     * 
     * @param {*} newFaqClickedStatus updates the status for the clicked element
     */
    function newFaqClickedStatus(newFaqClickedStatus){
        if(newFaqClickedStatus === 'open'){
            newStatus = 'closed';
        }
        
        if(newFaqClickedStatus === 'closed'){
            newStatus = 'open';
        }
        return newStatus; 

    }

    function addElementsState(newStateA, ourKeys, currentTargetIndex){    
        currentIndex.push({'clicked-index': parseInt(currentTargetIndex), 'accordian-state': 'closed', 'has-element-been-click-in-loop': false});
        faqState.clickedFaqElement = currentIndex; 
        // //need to loop through the array and make sure that i check if the element is added before i add it.
        //elementBeinglogged = [];
        // var i; 
        for( i = 0; i < faqState.clickedFaqElement.length; i++){
            
            //i need to find out if it was the first click or 

            //var elementKey = i;
            var element = faqState.clickedFaqElement[i];
            if( parseInt(faqState.clickedFaqElement[i]['clicked-index']) === parseInt(currentTargetIndex) ){

                var elementKey = i; 
                //changes the state of the accordian element when it is clicked. 
                accordianStatus = newFaqClickedStatus( element['accordian-state']);
                faqState.clickedFaqElement[elementKey] = {
                    'element-being-logged': elementKey, 
                    'clicked-index': parseInt(currentTargetIndex), 
                    'accordian-state': accordianStatus,
                    'has-element-been-click-in-loop': true};
                break;
                
            }

        }
    }
    
    
    //will trigger the faq being displayed.
    faqTrigger.addEventListener('click', function(event){
        event.preventDefault();

        //reseting states on button click
        // faqState.isFaqBeingDisplayed = false;
        // var doesEventHaveTarget = event.target.classList.contains('dlwfq-fq-target');
        var currentTarget = event.target;
        // getting the index of the element that is clicked on.
        var currentTargetIndex = currentTarget.getAttribute('data-index'); //gets the current index of the clicked element.
        
        //making sure that the user can click any where within the li and it will return an index back to me. 
        //TODO: - to make this more controlable in the future probably should return this a array of how far up the dom i had to climb from the target element to get too the data-index property.
        if(currentTargetIndex === null){
            currentTargetIndex = currentTarget.parentNode.getAttribute('data-index');
            if(currentTargetIndex === null){
                currentTargetIndex = currentTarget.parentNode.parentNode.getAttribute('data-index');
                if(currentTargetIndex === null){
                    currentTargetIndex = currentTarget.parentNode.parentNode.parentNode.getAttribute('data-index');
                    if(currentTargetIndex === null){
                        currentTargetIndex = currentTarget.parentNode.parentNode.parentNode.parentNode.getAttribute('data-index');
                        if(currentTargetIndex === null){
                            currentTargetIndex = currentTarget.parentNode.parentNode.parentNode.parentNode.parentNode.getAttribute('data-index');
                        }
                    }
                }
            }
        }
        
        addElementsState( faqState.clickedFaqElement, ['clicked-index', 'accordian-state'],  currentTargetIndex);
        
        // addNewStateIndex(currentTargetIndex);
        // check if we have any active faqs within are state object. 
        // for testing console.log(isArrayUndefined(faqState.clickedElementInfo)); 

        // //check if the current target is already within the active state, if the current target is not within the active state then we add it to the active state . 
        // else{
        //     console.log( checkElementWithinState(faqState.clickedElementInfo, 'clicked-index', currentTargetIndex) ); 
        // }

        //adding the states too the states object
        // addNewStateIndex(currentTargetIndex);
        
        
        //make sure that this is only added if the index is not within the current state. 
        // console.log(currentTargetIndex);
        // console.log(currentStates.clickedElementInfo);
        // faqState.clickedElementInfo.forEach(function(element){

        //     if( parseInt( element['clicked-index'] ) == currentTargetIndex){
        //         console.log(parseInt(element['clicked-index']) + ' already clicked');
        //     }else{
        //         console.log( parseInt( element['clicked-index'] )  + ' has not been clicked');
        //     }
            
        //     //console.log(element['clicked-index'] + 'from loop');
        //     //console.log(currentTargetIndex + 'from the element clicked'); 
        // } );
        //console.log(validclicks); 
        
        

        
        
        // displaying the faq content too the user if the target of the click contains dlwfq-fq-target. 
        // this will open up the faq an add in the states needed to track the users clicks on the faqs.
        // if(doesEventHaveTarget === true){ 
        
        //     // // change the state of content being displayed to true
        //     // faqState.isFaqBeingDisplayed = true;
        //     // var targetwithinscope = false;

        //     // //change the data-content-status too open
        //     // //event.target.parentNode.setAttribute('data-content-status', 'open');

        //     // //making sure that the faq-open class is only applied too the li element with a class of dlwsf-fq-target.
        //     // if(event.target.tagName === "li" ||  event.target.tagName === "LI" ){
        //     //     //add a class of dlwfq-faq-open
        //     //     event.target.classList.add("dlwfq-faq-open");
        //     //     targetwithinscope = true; 
        //     // }

        //     // //checking to see if the text within the li was clicked on. 
        //     // if(targetwithinscope === false){
        //     //     if(event.target.tagName === "span" ||  event.target.tagName === "SPAN"){

        //     //         //making sure that i have a parent of li so i add the dlwfq class too the correct element
        //     //         if(event.target.parentNode.tagName === "li" ||  event.target.parentNode.tagName === "LI"){
        //     //             event.target.parentNode.classList.add("dlwfq-faq-open"); 
        //     //             targetwithinscope = true;
                    
        //     //         }

        //     //     }
        //     // }

        //     // //this will excute when one of the above things are true.
        //     // if(targetwithinscope){
            
        //     // }

        //     // else{
        //     //     console.log('invalid click');
        //     // }

        // }

            //testing purposes only.
            //console.log($clickedElements);

        // }

        // //this should never run if it does then 
        // else{
        //     console.log('please contact wrightfloat.com/support');
        // }
        // end of making sure that the click was excuted within this section. 
        console.log(faqState); 


    } //end of the click event
                           
);