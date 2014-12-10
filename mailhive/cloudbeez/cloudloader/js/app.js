/*!
 * October Logic
 */

$(document).ready(function(){
    Installer.Pages.systemCheck.isRendered = true
    Installer.showPage(Installer.ActivePage, true)
})

var Installer = {
    ActivePage: 'systemCheck',
    Pages: {
        systemCheck:     { isStep1: true, body: 'check' },
//        configForm:      { isStep2: true, body: 'config' },
//        projectForm:     { isStep3: true, body: 'project' },
        installProgress: { isStep2: true, body: 'progress' },
        installComplete: { isStep3: true, body: 'complete' }
    },
    ActiveSection: null,
    Sections: {},
    Events: {},
    Data: {
        meta:   null, // Meta information from the server
        config: null, // Configuration from the user
        project: null // Project for the installation
    }
}

Installer.Events.retry = function() {
    var pageEvent = Installer.Pages[Installer.ActivePage].retry
    pageEvent && pageEvent()
}

Installer.Events.next = function() {
    var nextButton = $('#nextButton')
    if (nextButton.hasClass('disabled'))
        return

    var pageEvent = Installer.Pages[Installer.ActivePage].next
    pageEvent && pageEvent()
}

Installer.showPage = function(pageId, noPush) {
    $('html, body').scrollTop(0)
//    alert(pageId);
    var page = Installer.Pages[pageId],
        oldPage = (pageId != Installer.ActivePage) ? Installer.Pages[Installer.ActivePage] : null

    /*
     * Page events
     */
    oldPage && oldPage.beforeUnload && oldPage.beforeUnload()

    Installer.ActivePage = pageId

    page.beforeShow && page.beforeShow()

    $('#containerHeader').renderPartial('header', page)
    $('#containerTitle').renderPartial('title', page).find('.steps > .last.pass:first').addClass('animate fade_in')
    $('#containerFooter').renderPartial('footer', page)

    /*
     * Check if the content container exists already, if not, create it
     */
    var pageContainer = $('#containerBody').find('.pageContainer-' + pageId);
    if (!pageContainer.length) {
        pageContainer = $('<div />').addClass('pageContainer-' + pageId);
        pageContainer.renderPartial(page.body, page)
        $('#containerBody').append(pageContainer);
        page.init && page.init()
    }

    pageContainer.show().siblings().hide();

    // New page, add it to the history
    if (history.pushState && !noPush) {
        window.history.pushState({page:pageId}, '', window.location.pathname)
        page.isRendered = true
    }
}

Installer.setLoadingBar = function(state, message) {

    var progressBarContainer = $('#progressBar'),
        progressBar = $('#progressBar .progress-bar:first'),
        progressBarMessage = $('#progressBarMessage')

    if (message)
        progressBarMessage.html(message)

    progressBar.removeClass('progress-bar-danger')
    progressBarContainer.removeClass('failed')

    if (state == 'failed') {
        progressBar.addClass('progress-bar-danger').removeClass('animate infinite_loader')
        progressBarContainer.addClass('failed')
    }
    else if (state) {
        progressBarContainer.addClass('loading').removeClass('loaded')
        progressBar.addClass('animate infinite_loader')
    }
    else {
        progressBarContainer.addClass('loaded').removeClass('loading')
        progressBar.removeClass('animate infinite_loader')
    }
}

Installer.renderSections = function(sections, vars) {
    Installer.Sections = sections

    $.each(sections, function(index, section){
        Installer.renderSection(section, vars)
    })

    Installer.showSection(sections[0].code)
}

Installer.refreshSections = function(vars) {
    var stepContainer = $('#' + Installer.ActivePage)

    stepContainer.find('.section-area').remove()
    stepContainer.find('.section-side-nav:first').empty()

    $.each(Installer.Sections, function(index, section){
        Installer.renderSection(section, vars)
    })

    Installer.showSection(Installer.Sections[0].code)
}

Installer.renderSection = function(section, vars) {
    var sectionElement = $('<div />').addClass('section-area').attr('data-section-code', section.code),
        stepContainer = $('#' + Installer.ActivePage),
        container = stepContainer.find('.section-content:first')

    if (!section.category) section.category = "NULL"

    sectionElement
        .renderPartial(section.partial, vars)
        .prepend($('<h3 />').html(section.label))
        .hide()
        .appendTo(container)

    /*
     * Side navigation
     */
    var sideNav = stepContainer.find('.section-side-nav:first'),
        menuItem = $('<li />').attr('data-section-code', section.code),
        menuItemLink = $('<a />').attr({ href: "javascript:Installer.showSection('"+section.code+"')"}).html(section.label),
        sideNavCategory = sideNav.find('[data-section-category="'+section.category+'"]:first')

    if (sideNavCategory.length == 0) {
        sideNavCategory = $('<ul />').addClass('nav').attr('data-section-category', section.category)
        sideNavCategoryTitle = $('<h3 />').html(section.category)
        if (section.category == "NULL") sideNavCategoryTitle.html('')
        sideNav.append(sideNavCategoryTitle).append(sideNavCategory)
    }

    sideNavCategory.append(menuItem.append(menuItemLink))
}

Installer.renderSectionNav = function() {
    var
        stepContainer = $('#' + Installer.ActivePage),
        pageNav = stepContainer.find('.section-page-nav:first').empty(),
        sections = Installer.Sections

    $.each(sections, function(index, section){
        if (section.code == Installer.ActiveSection) {

            var nextStep = sections[index+1] ? sections[index+1] : null,
                lastStep = sections[index-1] ? sections[index-1] : null

            if (lastStep && Installer.isSectionVisible(lastStep.code)) {
                $('<a />')
                    .html(lastStep.label)
                    .addClass('btn btn-default prev')
                    .attr('href', "javascript:Installer.showSection('"+lastStep.code+"')")
                    .appendTo(pageNav)
            }

            if (nextStep && Installer.isSectionVisible(nextStep.code)) {
                $('<a />')
                    .html(nextStep.label)
                    .addClass('btn btn-default next')
                    .attr('href', "javascript:Installer.showSection('"+nextStep.code+"')")
                    .appendTo(pageNav)
            }

            return false
        }
    })
}

Installer.showSection = function(code) {
    var
        stepContainer = $('#' + Installer.ActivePage),
        sideNav = stepContainer.find('.section-side-nav:first'),
        menuItem = sideNav.find('[data-section-code="'+code+'"]:first'),
        container = stepContainer.find('.section-content:first'),
        sectionElement = container.find('[data-section-code="'+code+'"]:first')

    sideNav.find('li.active').removeClass('active')
    menuItem.addClass('active')
    sectionElement.show().siblings().hide()

    Installer.ActiveSection = code
    Installer.renderSectionNav()
}

Installer.toggleSection = function(code, state) {
    var
        stepContainer = $('#' + Installer.ActivePage),
        sideNav = stepContainer.find('.section-side-nav:first'),
        menuItem = sideNav.find('[data-section-code="'+code+'"]:first'),
        container = stepContainer.find('.section-content:first'),
        sectionElement = container.find('[data-section-code="'+code+'"]:first')

    if (state) {
        menuItem.show()
        sectionElement.show()
    }
    else {
        menuItem.hide()
        sectionElement.hide()
    }
}

Installer.isSectionVisible = function(code) {
    return $('#' + Installer.ActivePage + ' [data-section-code="'+code+'"]:first').is(':visible')
}

$.fn.extend({
    renderPartial: function(name, data, options) {
        var container = $(this),
            template = $('[data-partial="' + name + '"]'),
            contents = Mustache.to_html(template.html(), data)

        options = $.extend(true, {
            append: false
        }, options)

        if (options.append) container.append(contents)
        else container.html(contents)
        return this
    },

    sendRequest: function(handler, data, options) {
        var form = $(this),
            postData = form.serializeObject(),
            controlPanel = $('#formControlPanel'),
            nextButton = $('#nextButton')

        // MailBeez
        // set context for request
        postData["cloudloader_mode"] = window.cloudloader_mode;

        options = $.extend(true, {
            loadingIndicator: true
        }, options)

        if (options.loadingIndicator) {
            nextButton.attr('disabled', true)
            controlPanel.addClass('loading')
        }

        if (!data)
            data = {handler: handler}
        else
            data.handler = handler

        if (data)
            $.extend(postData, data)

        var postObj = $.post(window.location.pathname, postData)
        postObj.always(function(){
            if (options.loadingIndicator) {
                nextButton.attr('disabled', false)
                controlPanel.removeClass('loading')
            }
        })
        return postObj
    },

    serializeObject: function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }
})

$.extend({
    sendRequest: function(handler, data, options) {
        return $('<form />').sendRequest(handler, data, options)
    }
})

window.onpopstate = function(event) {
    // If progress page has rendered, disable navigation
    if (Installer.Pages.installProgress.isRendered) {
        // Do nothing
    }
    // Navigate back/foward through a known push state
    else if (event.state) {
        // Only allow navigation to previously rendered pages
        var noPop = (!Installer.Pages[event.state.page].isRendered || Installer.ActivePage == event.state.page)
        if (!noPop)
            Installer.showPage(event.state.page, true)
    }
    // Otherwise show the first page, if not already on it
    else if (Installer.ActivePage != 'systemCheck') {
        Installer.showPage('systemCheck', true)
    }
}
