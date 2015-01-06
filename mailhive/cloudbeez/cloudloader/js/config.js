/*!
 * Configuration Form (Step 2)
 */

Installer.Pages.configForm.activeCategory = null

Installer.Pages.configForm.init = function() {
    var configForm = $('#configForm').addClass('animate fade_in')

    Installer.renderSections(Installer.Pages.configForm.sections)

    var configFormFailed = $('#configFormFailed').hide(),
        configFormDatabase = $('#configFormDatabase')

    configFormDatabase.renderPartial('config/mysql')

    // Set the encryption code with a random string
    $('#advEncryptionCode').val(Math.random().toString(36).slice(2))
}

Installer.Pages.configForm.next = function() {

    var eventChain = [],
        configFormFailed = $('#configFormFailed').hide().removeClass('animate fade_in')

    Installer.Data.config = $('#configFormElement').serializeObject()

    $('.section-area').removeClass('fail')

    /*
     * Validate each section
     */
    $.each(Installer.Pages.configForm.sections, function(index, section){
        eventChain.push(function() {
            return $('#configFormElement').sendRequest(section.handler).fail(function(data){

                configFormFailed.show().addClass('animate fade_in')
                configFormFailed.renderPartial('config/fail', { label: section.label, reason: data.responseText })

                var sectionElement = $('.section-area[data-section-code="'+section.code+'"]').addClass('fail')
                configFormFailed.appendTo(sectionElement)

                Installer.showSection(section.code)

                // Scroll browser to the bottom of the error
                var scrollTo = configFormFailed.offset().top - $(window).height() + configFormFailed.height() + 10
                $('body, html').animate({ scrollTop: scrollTo })
            })
        })
    })

    $.waterfall.apply(this, eventChain).done(function(){
        Installer.showPage('projectForm')
    })
}

Installer.Pages.configForm.toggleDatabase = function(el) {
    var selectedValue = $(el).val(),
        configFormDatabase = $('#configFormDatabase'),
        databasePartial = 'config/' + selectedValue

    configFormDatabase.renderPartial(databasePartial)
}
