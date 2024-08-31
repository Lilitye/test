$(document).ready(function() {

    $(function() {
        let smartWizard = $("#smartwizard");

        smartWizard.smartWizard({
            theme: 'arrows',
            enableUrlHash: false,
            enableFinishButton: true,
            toolbar: {
                extraHtml: '<button type="button" class="btn btn-danger finish-btn">Finish</button>'
            },
        });

        const myModal = new bootstrap.Modal($('#confirmModal'));

        $(document).on("click", ".finish-btn", function () {
           let currentStepIdx = $('.step-item').length;
           if(!validateStep(currentStepIdx)) {
               return false;
           }
           myModal.show();
        });

        smartWizard.on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
            if(stepNumber === 0) {
                $(".sw-btn-prev").hide();
            } else {
                $(".sw-btn-prev").show();
            }
            if(stepNumber === $('.step-item').length){
                $('.finish-btn').show();
                $(".sw-btn-next").hide();
            }else{
                $('.finish-btn').hide(); //todo
                $(".sw-btn-next").show();
            }

        });

        smartWizard.on("leaveStep", function(e, anchorObject, currentStepIdx, nextStepIdx, stepDirection) {
            if (stepDirection === 'forward'/* && currentStepIdx > 0*/) {
                return validateStep(currentStepIdx);
            }
        });

        let validateStep = function (currentStepIdx) {
            let step = $('#step-' + (currentStepIdx));
            if (step) {
                if(currentStepIdx === 0) {
                    if(!$("form")[0].checkValidity()) {
                        smartWizard.smartWizard("setState", [currentStepIdx], 'error');
                        smartWizard.smartWizard('fixHeight');

                        $("form")[0].classList.add('was-validated');

                        return false;
                    }
                } else {
                    step.find(".invalid-feedback").hide();

                    if (!(step.find('.answer-option:checked').length)) {
                        step.find(".invalid-feedback").show();

                        smartWizard.smartWizard("setState", [currentStepIdx], 'error');
                        smartWizard.smartWizard('fixHeight');

                        return false; //todo
                    }
                }

                smartWizard.smartWizard("unsetState", [currentStepIdx], 'error');

                return true;
            }
        }
    });

    $('#confirm-btn').click(function () {
        $('#wizard-form input:disabled').prop("disabled", false);
        $('#wizard-form').submit();
    });
});