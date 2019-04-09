    
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
     * @param string newFaqClickedStatus updates the status for the clicked element
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

    function addElementsState(currentTargetIndex){    
        currentIndex.push({'clicked-index': parseInt(currentTargetIndex), 'accordian-state': 'closed', 'has-element-been-click-in-loop': false});
        faqState.clickedFaqElement = currentIndex;
    }

    /**
     * 
     * this function is responsible for toggling the class's for our accordian. 
     * 
     * @param event theTarget - Passing the current clicked element event   
     * @param int currentParentNode  - Passing how many nodes this click element is from the li element. This is used so we can add or remove our class when the element is clicked. 
     * @param string addOrRemove - Toggles the class based on the clicked elements current state of the accordian-state. (defaults to open will but will except the value of closed as well) 
     * @param string toggleClass - The class to remove or add to the li element. 
     */
    function toggleOurClass(theTarget, currentParentNode, addOrRemove = 'open', toggleClass = 'dlwfq-faq-open'){
        //TODO: make sure we validate things that can be set by a user. 
        switch (currentParentNode) {
            //will be used when the current target has no parent elements 
            case 0:
                //responsible for adding the class
                
                if(theTarget.tagName === "li" ||  theTarget.tagName === "LI" ){
                    //adds the class
                    if(addOrRemove === 'open'){
                        theTarget.classList.add(toggleClass); 
                    }
                    else{
                        theTarget.classList.remove(toggleClass); 
                    }
                }
                break;
            
            //will be used when the current target has a parent node of 1 
            case 1:
               
                if(theTarget.parentNode.tagName === "li" ||  theTarget.parentNode.tagName === "LI" ){
                    //adds the class
                    if(addOrRemove === 'open'){
                        theTarget.parentNode.classList.add(toggleClass); 
                    }
                    else{
                        theTarget.parentNode.classList.remove(toggleClass); 
                    }
                }
                break;

            
            //will be used when the current target has a parent node of 2
            case 2:
                
                if(theTarget.parentNode.parentNode.tagName === "li" ||  theTarget.parentNode.parentNode.tagName === "LI" ){
                    //adds the class
                    if(addOrRemove === 'open'){
                        theTarget.parentNode.parentNode.classList.add(toggleClass); 
                    }
                    else{
                        theTarget.parentNode.parentNode.classList.remove(toggleClass); 
                    }
                }
                break;
            
            //will be used when the current target has a parent node of 3
            case 3:
                if(theTarget.parentNode.parentNode.parentNode.tagName === "li" ||  theTarget.parentNode.parentNode.parentNode.tagName === "LI" ){
                    //adds the class
                    if(addOrRemove === 'open'){
                        theTarget.parentNode.parentNode.parentNode.classList.add(toggleClass); 
                    }
                    else{
                        theTarget.parentNode.parentNode.parentNode.classList.remove(toggleClass); 
                    }
                }
                break;
            
            //will be used when the current target has a parent node of 4
            case 4: 
            
                if(theTarget.parentNode.parentNode.parentNode.parentNode.tagName === "li" ||  theTarget.parentNode.parentNode.parentNode.parentNode.tagName === "LI" ){
                    //adds the class
                    if(addOrRemove === 'open'){
                        theTarget.parentNode.parentNode.parentNode.parentNode.classList.add(toggleClass); 
                    }
                    else{
                        theTarget.parentNode.parentNode.parentNode.parentNode.classList.remove(toggleClass); 
                    }
                }
                break;
            
            //will be used when the current target has a parent node of 5
            case 5:


                if(theTarget.parentNode.parentNode.parentNode.parentNode.parentNode.tagName === "li" ||  theTarget.parentNode.parentNode.parentNode.parentNode.parentNode.tagName === "LI" ){
                    //adds the class
                    if(addOrRemove === 'open'){
                        theTarget.parentNode.parentNode.parentNode.parentNode.parentNode.classList.add(toggleClass); 
                    }else{
                        theTarget.parentNode.parentNode.parentNode.parentNode.parentNode.classList.remove(toggleClass);
                    }
                }
            break;
        }
    }
    
    
    //will trigger the faq being displayed or closing it depending on wether it was already open.
    faqTrigger.addEventListener('click', function(event){
        event.preventDefault();
        
        //making sure that the current taget contains the dlwfq class 
        var currentTarget = event.target;
        var targetClass = 'dlwfq-fq-target'; 
        //TODO: can be used to make this more dynamic later on.

        //climbs the dom to see if the element clicked is within the target element that we are looking for. 
        var doesEventHaveTarget = { 
            'target': currentTarget.classList.contains(targetClass),
            'currentparentNode': 0, 
            'currentTargetIndex': parseInt(currentTarget.getAttribute('data-index'))
        };
        if(doesEventHaveTarget.target === false){
            doesEventHaveTarget = { 
                'target': currentTarget.parentNode.classList.contains(targetClass),
                'currentparentNode': 1,
                'currentTargetIndex': parseInt(currentTarget.parentNode.getAttribute('data-index'))
            };
            if(doesEventHaveTarget.target === false){
                doesEventHaveTarget = { 'target': currentTarget.parentNode.parentNode.classList.contains(targetClass),
                'currentparentNode': 2,
                'currentTargetIndex': parseInt(currentTarget.parentNode.parentNode.getAttribute('data-index'))
                };
                if(doesEventHaveTarget.target === false){
                    doesEventHaveTarget = { 
                        'target': currentTarget.parentNode.parentNode.parentNode.classList.contains(targetClass),
                        'currentparentNode': 3, 
                        'currentTargetIndex': parseInt(currentTarget.parentNode.parentNode.parentNode.getAttribute('data-index'))
                    };
                    if(doesEventHaveTarget.target === false){
                        doesEventHaveTarget = { 
                            'target': currentTarget.parentNode.parentNode.parentNode.parentNode.classList.contains(targetClass), 
                            'currentparentNode': 4, 
                            'currentTargetIndex': parseInt(currentTarget.parentNode.parentNode.parentNode.parentNode.getAttribute('data-index'))
                        };
                        if(doesEventHaveTarget.target === false){
                            doesEventHaveTarget = { 
                                'target': currentTarget.parentNode.parentNode.parentNode.parentNode.parentNode.classList.contains(targetClass), 
                                'currentparentNode': 5, 
                                'currentTargetIndex': parseInt(currentTarget.parentNode.parentNode.parentNode.parentNode.parentNode.getAttribute('data-index'))
                            };
                        }
                        else{
                            doesEventHaveTarget = { 'target': 'does not seem to be a valid target'};
                        }
                    }
                }
            }
        }

        //making sure that the click is a valid click before adding anything into are state. 
        if(doesEventHaveTarget.target){

            //adds the elements to the state array
            addElementsState(doesEventHaveTarget.currentTargetIndex);

            //need a check to see if we need too loop through the elements within the state array, so we can update the status
            if(faqState.clickedFaqElement.length > 1){
                var i;
                for( i = 0; i < faqState.clickedFaqElement.length; i++){
                    //var elementKey = i;
                    var element = faqState.clickedFaqElement[i];
                    if( parseInt(faqState.clickedFaqElement[i]['clicked-index']) === parseInt(doesEventHaveTarget.currentTargetIndex) ){

                        var elementKey = i; 
                        //changes the state of the accordian element when it is clicked. 
                        accordianStatus = newFaqClickedStatus( element['accordian-state']);
                        
                        //this will give me an active state that i can use.
                        faqState.clickedFaqElement[elementKey] = {
                            'element-being-logged': elementKey, 
                            'clicked-index': parseInt(doesEventHaveTarget.currentTargetIndex), 
                            'accordian-state': accordianStatus,
                            'has-element-been-click-in-loop': true
                        };

                        //this will update the class. 
                        toggleOurClass(currentTarget, doesEventHaveTarget.currentparentNode, faqState.clickedFaqElement[elementKey]['accordian-state']);
                        break;   
                    }
                }
            }
            //this only runs when i have only one element clicked, otherwise this will never run. 
            else{
                faqState.clickedFaqElement[0]['accordian-state'] = 'open';
                toggleOurClass(currentTarget, doesEventHaveTarget.currentparentNode, faqState.clickedFaqElement[0]['accordian-state']);
            }

        }

    } //end of the click event
                           
);