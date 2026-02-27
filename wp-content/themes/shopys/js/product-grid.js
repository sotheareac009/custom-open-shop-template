/**
 * Premium Product Grid - Filter & Interactions
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initCategoryFilter();
        initInfiniteScroll();
    });

    function initInfiniteScroll() {
        const containers = document.querySelectorAll('.ppg-container');

        containers.forEach(function (container) {
            const pagination = container.querySelector('.ppg-pagination[data-type="infinite"]');
            if (!pagination) return;

            const grid = container.querySelector('.ppg-grid');
            const loader = pagination.querySelector('.ppg-infinite-loader');
            const linksContainer = pagination.querySelector('.ppg-pagination-links');
            let isFetching = false;

            // Use Intersection Observer for infinite scrolling
            const observer = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting && !isFetching) {
                    loadNextPage();
                }
            }, { rootMargin: '0px 0px 300px 0px' });

            if (loader) {
                observer.observe(loader);
            }

            function loadNextPage() {
                const nextLink = linksContainer.querySelector('.next.page-numbers');
                if (!nextLink) {
                    // No more pages
                    loader.innerHTML = '<span style="color: #aaa; margin-top: 10px; display: block;">No more products to show.</span>';
                    observer.disconnect();
                    return;
                }

                const nextUrl = nextLink.getAttribute('href');
                isFetching = true;

                fetch(nextUrl)
                    .then(function (response) { return response.text(); })
                    .then(function (html) {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');

                        // Find new cards
                        const newCards = doc.querySelectorAll('.ppg-card');
                        let visibleIndex = 0;

                        newCards.forEach(function (card) {
                            grid.appendChild(card);

                            // Initialize animation for new cards
                            card.style.animation = 'none';
                            card.offsetHeight; // trigger reflow
                            card.style.animation = 'ppgFadeIn 0.4s ease-out ' + (visibleIndex * 0.05) + 's both';
                            visibleIndex++;
                        });

                        // Re-evaluate Category Filter for new cards
                        const activeFilter = container.querySelector('.ppg-filter-btn.active');
                        if (activeFilter && activeFilter.getAttribute('data-category') !== 'all') {
                            const cat = activeFilter.getAttribute('data-category');
                            newCards.forEach(function (card) {
                                const cardCats = card.getAttribute('data-categories') || '';
                                if (cardCats.indexOf(cat) === -1) {
                                    card.classList.add('ppg-hidden');
                                }
                            });
                        }

                        // Update pagination wrapper internal links
                        const newLinksContainer = doc.querySelector('.ppg-pagination-links');
                        if (newLinksContainer) {
                            linksContainer.innerHTML = newLinksContainer.innerHTML;
                        } else {
                            linksContainer.innerHTML = '';
                            loadNextPage(); // Trigger "No more products"
                        }

                        isFetching = false;
                    })
                    .catch(function (error) {
                        console.error('PPG Infinite Scroll Error:', error);
                        isFetching = false;
                    });
            }
        });
    }

    function initCategoryFilter() {
        const filterBtns = document.querySelectorAll('.ppg-filter-btn');

        if (!filterBtns.length) return;

        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const category = this.getAttribute('data-category');
                // The parent container scope is needed if multiple grids exist
                const container = this.closest('.ppg-container');
                const cards = container.querySelectorAll('.ppg-card');

                // Update active state
                container.querySelectorAll('.ppg-filter-btn').forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');

                // Filter cards with animation
                let visibleIndex = 0;
                cards.forEach(function (card) {
                    const cardCategories = card.getAttribute('data-categories') || '';

                    if (category === 'all' || cardCategories.indexOf(category) !== -1) {
                        card.classList.remove('ppg-hidden');
                        card.style.animation = 'none';
                        card.offsetHeight; // trigger reflow
                        card.style.animation = 'ppgFadeIn 0.4s ease-out ' + (visibleIndex * 0.05) + 's both';
                        visibleIndex++;
                    } else {
                        card.classList.add('ppg-hidden');
                    }
                });
            });
        });
    }
})();
