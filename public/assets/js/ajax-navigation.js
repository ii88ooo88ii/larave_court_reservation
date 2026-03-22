$(document).ready(function() {
    // Handle all navigation links
    $(document).on('click', 'a:not([data-no-ajax])', function(e) {
        // Skip if it's an external link or has target="_blank"
        if (this.target === '_blank' || this.href.indexOf(window.location.origin) !== 0) {
            return true;
        }
        
        // Skip if it's a file download, anchor link, or has no-ajax class
        if (this.hasAttribute('download') || this.hash !== '' || $(this).hasClass('no-ajax')) {
            return true;
        }
        
        // Skip if it's a bootstrap dropdown toggle or collapse
        if ($(this).attr('data-toggle') === 'dropdown' || $(this).attr('data-toggle') === 'collapse') {
            return true;
        }
        
        e.preventDefault();
        
        var url = this.href;
        var title = $(this).text() || document.title;
        
        // Update URL in browser without reload
        history.pushState({ url: url }, title, url);
        
        // Load content
        loadContent(url);
    });
    
    // Handle browser back/forward buttons
    $(window).on('popstate', function(e) {
        if (e.originalEvent.state && e.originalEvent.state.url) {
            loadContent(e.originalEvent.state.url);
        } else {
            loadContent(window.location.href);
        }
    });
    
    // Function to load content via AJAX
    function loadContent(url) {
        // Show loading indicator
        showLoading();
        
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'html',
            success: function(html) {
                // Extract the content area from the response
                var $html = $(html);
                var newContent = $html.find('#page-content-wrapper').html();
                
                if (newContent) {
                    // Update content
                    $('#page-content-wrapper').html(newContent);
                } else {
                    // Fallback: try to get the main content
                    var mainContent = $html.find('.container-fluid').html();
                    if (mainContent) {
                        $('#page-content-wrapper').html(mainContent);
                    } else {
                        $('#page-content-wrapper').html(html);
                    }
                }
                
                // Update page title
                var newTitle = $html.filter('title').text();
                if (newTitle) {
                    document.title = newTitle;
                }
                
                // Hide loading indicator
                hideLoading();
            },
            error: function() {
                // If AJAX fails, fallback to normal page load
                hideLoading();
                window.location.href = url;
            }
        });
    }
    
    // Show loading indicator
    function showLoading() {
        if ($('#loading-overlay').length === 0) {
            $('body').append(`
                <div id="loading-overlay" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
                    <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
            `);
        } else {
            $('#loading-overlay').show();
        }
    }
    
    function hideLoading() {
        $('#loading-overlay').hide();
    }
});