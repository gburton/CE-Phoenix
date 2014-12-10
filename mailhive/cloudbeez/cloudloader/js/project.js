/*!
 * Packages & Install (Step 3)
 */

// Expected:
//   [{ code: 'October.Demo', name: 'Demo', author: 'October', description: 'Demonstration features.', image: 'http://placehold.it/100x100' }, ...]
Installer.Pages.projectForm.includedPlugins = []
Installer.Pages.projectForm.includedThemes = []
Installer.Pages.projectForm.suggestedPlugins = []
Installer.Pages.projectForm.suggestedThemes = []

Installer.Pages.projectForm.init = function() {

    var projectForm = $('#projectForm').addClass('animate fade_in')

    Installer.renderSections(Installer.Pages.projectForm.sections)

    $('#suggestedProductsContainer').hide()

    Installer.Pages.projectForm.bindAll()

    /*
     * Hide the project section initially
     */
    Installer.toggleSection('project')
    Installer.showSection('plugins')
}

Installer.Pages.projectForm.next = function() {
    Installer.showPage('installProgress')
}

Installer.Pages.projectForm.bindAll = function() {
    Installer.Pages.projectForm.bindSearch('#pluginSearchInput')
    Installer.Pages.projectForm.bindSearch('#themeSearchInput')

    Installer.Pages.projectForm.bindSuggested('#suggestedPlugins')
    Installer.Pages.projectForm.bindSuggested('#suggestedThemes')

    Installer.Pages.projectForm.bindIncludeManager('#pluginList')
    Installer.Pages.projectForm.bindIncludeManager('#themeList')
}

Installer.Pages.projectForm.bindSearch = function(el) {
    var $el = $(el),
        $form = $el.closest('form'),
        handler = $el.data('handler')

    if ($el.length == 0) return

    // Template for search results
    var template = Mustache.compile([
        '<div class="product-details">',
        '<div class="product-image"><img src="{{image}}" alt=""></div>',
        '<div class="product-name ">{{name}}</div>',
        '<div class="product-description text-overflow">{{description}}</div>',
        '</div>'
    ].join(''))

    // Source for product search
    var engine = new Bloodhound({
        name: 'products',
        remote: window.location.pathname + '?handler=' + handler + '&query=%QUERY',
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.val)
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        limit: 5
    })

    engine.initialize()

    /*
     * Bind autocomplete to search field
     */
    $(el)
        .typeahead(null, {
            displayKey: 'code',
            source: engine.ttAdapter(),
            minLength: 3,
            templates: {
                suggestion: template
            }
        })
        .on('typeahead:opened', function(){
            $(el + ' .tt-dropdown-menu').css('width', $(el).width() + 'px')
        })
        .on('typeahead:selected', function(object, datum){
            $form.submit()
        })
        .on('keypress', function(e) {
            if (e.keyCode == 13) // ENTER key
                $form.submit()
        })

}

Installer.Pages.projectForm.searchSubmit = function(el) {
    var
        $el = $(el),
        $input = $el.find('.product-search-input.tt-input:first')

    Installer.Pages.projectForm.includePackage(el, $input.val())
    $input.typeahead('val', '')
}

Installer.Pages.projectForm.detachProject = function(el) {
    if (!confirm('Are you sure?')) return

    Installer.Data.project = null
    Installer.Pages.projectForm.includedPlugins = []
    Installer.Pages.projectForm.includedThemes = []
    Installer.refreshSections()
    Installer.Pages.projectForm.bindAll()
}

Installer.Pages.projectForm.attachProject = function(el) {
    var
        $el = $(el),
        $input = $el.find('.project-id-input:first'),
        code = $input.val(),
        projectFormFailed = $('#projectFormFailed').hide().removeClass('animate fade_in')

    $.sendRequest('onProjectDetails', { code: code })
        .done(function(result){
            Installer.Data.project = result
            Installer.Data.project.code = code
            Installer.Pages.projectForm.includedPlugins = result.plugins ? result.plugins : []
            Installer.Pages.projectForm.includedThemes = result.themes ? result.themes : []
            Installer.refreshSections({
                projectId: code,
                projectName: result.name,
                projectOwner: result.owner,
                projectDescription: result.description
            })
            Installer.Pages.projectForm.bindAll()
        })
        .fail(function(data){
            projectFormFailed.show().addClass('animate fade_in')
            projectFormFailed.renderPartial('project/fail', { reason: data.responseText })
        })
}

Installer.Pages.projectForm.bindSuggested = function(el) {

    var
        dataSetId = $(el).data('set'),
        productSet = Installer.Pages.projectForm[dataSetId]

    /*
     * If no suggested extras are provided, pull them from the server
     */
    if (!productSet || productSet.length == 0) {
        var handler = $(el).data('handler')
        $.sendRequest(handler, {}, { loadingIndicator: false }).done(function(result){
            if (!$.isArray(result)) return
            productSet = result
            Installer.Pages.projectForm.renderSuggested(el, productSet)
        })
    }
    else {
        Installer.Pages.projectForm.renderSuggested(el, productSet)
    }
}

Installer.Pages.projectForm.renderSuggested = function(el, suggestedProducts) {
    var $el = $(el),
        $container = $el.closest('.suggested-products-container')

    if (suggestedProducts.length == 0) {
        $container.hide()
    }
    else {
        $container.show().addClass('animate fade_in')
        $.each(suggestedProducts, function(index, product){
            $el.renderPartial('project/suggestion', product, { append:true })
        })
    }
}

Installer.Pages.projectForm.bindIncludeManager = function(el) {
    var
        $el = $(el),
        $list = $el.find('.product-list:first'),
        $empty = $el.find('.product-list-empty:first'),
        $counter = $el.find('.product-counter:first'),
        partial = $el.data('view'),
        dataSetId = $el.data('set'),
        includedProducts = Installer.Pages.projectForm[dataSetId]

    if (!$el.length)
        return

    if (includedProducts.length == 0) {
        $empty.show()
    }
    else {
        $.each(includedProducts, function(index, product){
            $list.renderPartial(
                partial,
                $.extend(true, {}, product, { projectId: Installer.Data.project.code }),
                { append: true }
            )
        })
        $empty.hide()
    }

    $counter.text(includedProducts.length)
}

Installer.Pages.projectForm.findIncludeManagerFromEl = function(el) {
    var $el = $(el)

    if ($el.hasClass('product-list-manager'))
        return el

    return $(el).closest('[data-manager]').data('manager')
}

Installer.Pages.projectForm.includePackage = function(el, code) {
    var
        $el = $(Installer.Pages.projectForm.findIncludeManagerFromEl(el)),
        $list = $el.find('.product-list:first'),
        $empty = $el.find('.product-list-empty:first'),
        $counter = $el.find('.product-counter:first'),
        handler = $el.data('handler'),
        partial = $el.data('view'),
        dataSetId = $el.data('set'),
        includedProducts = Installer.Pages.projectForm[dataSetId],
        productExists = false,
        extraData = { name: code }

    if (Installer.Data.project && Installer.Data.project.code)
        extraData.project = Installer.Data.project.code

    $.each(includedProducts, function(index, product){
        if (product.code == code)
            productExists = true
    })

    if (productExists)
        return

    $.sendRequest(handler, extraData)
        .done(function(product){
            includedProducts.push(product)
            $empty.hide()
            $list.renderPartial(partial, product, { append:true })
            $counter.text(includedProducts.length)
            Installer.Pages.projectForm.hilightIncludedPackages($el)
        })
        .fail(function(data){
            alert(data.responseText)
        })
}

Installer.Pages.projectForm.removePackage = function(el, code) {
    var
        $el = $(Installer.Pages.projectForm.findIncludeManagerFromEl(el)),
        $counter = $el.find('.product-counter:first'),
        $empty = $el.find('.product-list-empty:first'),
        dataSetId = $el.data('set'),
        includedProducts = Installer.Pages.projectForm[dataSetId]

    $el.find('[data-code="'+code+'"]').fadeOut(500, function(){

        Installer.Pages.projectForm[dataSetId] = includedProducts = $.grep(includedProducts, function(product) {
            return product.code != code;
        })

        if (includedProducts.length == 0) $empty.show()
        $counter.text(includedProducts.length)

        $(this).remove()
        $('[data-code="'+code+'"]').removeClass('product-included')
    })
}

Installer.Pages.projectForm.hilightIncludedPackages = function(el) {
    var
        $el = $(el),
        dataSetId = $el.data('set'),
        includedProducts = Installer.Pages.projectForm[dataSetId]

    $.each(includedProducts, function(index, product){
        $('[data-code="'+product.code+'"]').addClass('product-included')
    })
}

Installer.Pages.projectForm.showProject = function() {
    $('.btn-show-project').hide()
    Installer.toggleSection('project', true)
    Installer.showSection('project')
}

Installer.Pages.projectForm.showHelp = function(el) {
    $('#projectFormHelp').slideToggle()
}