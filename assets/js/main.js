/**
 * Template Name: Company
 * Template URL: https://bootstrapmade.com/company-free-html-bootstrap-template/
 * Author: BootstrapMade.com
 * License: https://bootstrapmade.com/license/
 */
(function () {
  "use strict";

  /**
   * Easy selector helper function
   */
  const select = (el, all = false) => {
    el = el.trim();
    if (all) {
      return [...document.querySelectorAll(el)];
    } else {
      return document.querySelector(el);
    }
  };

  /**
   * Easy event listener function
   */
  const on = (type, el, listener, all = false) => {
    let selectEl = select(el, all);
    if (selectEl) {
      if (all) {
        selectEl.forEach((e) => e.addEventListener(type, listener));
      } else {
        selectEl.addEventListener(type, listener);
      }
    }
  };

  /**
   * Easy on scroll event listener
   */
  const onscroll = (el, listener) => {
    el.addEventListener("scroll", listener);
  };

  /**
   * Back to top button
   */
  let backtotop = select(".back-to-top");
  if (backtotop) {
    const toggleBacktotop = () => {
      if (window.scrollY > 100) {
        backtotop.classList.add("active");
      } else {
        backtotop.classList.remove("active");
      }
    };
    window.addEventListener("load", toggleBacktotop);
    onscroll(document, toggleBacktotop);
  }

  /**
   * Mobile nav toggle
   */
  on("click", ".mobile-nav-toggle", function (e) {
    select("#navbar").classList.toggle("navbar-mobile");
    this.classList.toggle("bi-list");
    this.classList.toggle("bi-x");
  });

  /**
   * Mobile nav dropdowns activate
   */
  on(
    "click",
    ".navbar .dropdown > a",
    function (e) {
      if (select("#navbar").classList.contains("navbar-mobile")) {
        e.preventDefault();
        this.nextElementSibling.classList.toggle("dropdown-active");
      }
    },
    true
  );

  /**
   * Hero carousel indicators
   */
  let heroCarouselIndicators = select("#hero-carousel-indicators");
  let heroCarouselItems = select("#heroCarousel .carousel-item", true);

  heroCarouselItems.forEach((item, index) => {
    index === 0
      ? (heroCarouselIndicators.innerHTML +=
          "<li data-bs-target='#heroCarousel' data-bs-slide-to='" +
          index +
          "' class='active'></li>")
      : (heroCarouselIndicators.innerHTML +=
          "<li data-bs-target='#heroCarousel' data-bs-slide-to='" +
          index +
          "'></li>");
  });

  /**
   * Clients carousel indicators
   */
  let clientCarouselIndicators = select("#client-carousel-indicators");
  let clientCarouselItems = select("#clientCarousel .carousel-item", true);

  clientCarouselItems.forEach((item, index) => {
    index === 0
      ? (clientCarouselIndicators.innerHTML +=
          "<li data-bs-target='#clientCarousel' data-bs-slide-to='" +
          index +
          "' class='active'></li>")
      : (clientCarouselIndicators.innerHTML +=
          "<li data-bs-target='#clientCarousel' data-bs-slide-to='" +
          index +
          "'></li>");
  });

  /**
   * Porfolio isotope and filter
   */
  window.addEventListener("load", () => {
    let portfolioContainer = select(".portfolio-container");
    if (portfolioContainer) {
      let portfolioIsotope = new Isotope(portfolioContainer, {
        itemSelector: ".portfolio-item",
      });

      let portfolioFilters = select("#portfolio-flters li", true);

      on(
        "click",
        "#portfolio-flters li",
        function (e) {
          e.preventDefault();
          portfolioFilters.forEach(function (el) {
            el.classList.remove("filter-active");
          });
          this.classList.add("filter-active");

          portfolioIsotope.arrange({
            filter: this.getAttribute("data-filter"),
          });
          portfolioIsotope.on("arrangeComplete", function () {
            AOS.refresh();
          });
        },
        true
      );
    }
  });

  /**
   * Initiate portfolio lightbox
   */
  const portfolioLightbox = GLightbox({
    selector: ".portfolio-lightbox",
  });

  new Swiper(".home-swiper", {
    speed: 2000,
    loop: true,
    autoplay: {
      delay: 10000,
      disableOnInteraction: false,
    }, 
    slidesPerView: "1",
    effect: 'coverflow',
    grabCursor: true,
    centeredSlides: true,
    coverflowEffect: {
      rotate: 50, // Set rotation angle
      stretch: -100, // Stretch slides
      depth: 100, // Depth of the effect
      modifier: 1, // Increase to make effect more visible
      slideShadows: true, // Add shadows for better effect
    },
    pagination: {
      el: ".swiper-pagination",
      type: "bullets",
      clickable: true,
      autoplay: {
        delay: 10000, // Bullets change every second
        disableOnInteraction: false,
    },
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

  new Swiper(".banner-slider", {
    speed: 1000,
    loop: true,
    autoplay: {
      delay: 10000,
      disableOnInteraction: false,
    },
    slidesPerView: "1",
    pagination: {
      el: ".swiper-pagination",
      type: "bullets",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
  });

  /**
   * Skills animation
   */
  let skilsContent = select(".skills-content");
  if (skilsContent) {
    new Waypoint({
      element: skilsContent,
      offset: "80%",
      handler: function (direction) {
        let progress = select(".progress .progress-bar", true);
        progress.forEach((el) => {
          el.style.width = el.getAttribute("aria-valuenow") + "%";
        });
      },
    });
  }

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: ".glightbox",
  });

  /**
   * Initiate gallery lightbox
   */
  const galleryLightbox = GLightbox({
    selector: ".gallery-lightbox",
  });

  /**
   *  testimonial sliders   *
   */
  new Swiper(".client-slider", {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    slidesPerView: "2",
    pagination: {
      el: ".swiper-pagination",
      type: "bullets",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      100: {
        slidesPerView: 1, // Show only one testimonial on smaller screens
        spaceBetween: 10,
      },
      520: {
        slidesPerView: 2, // Show only one testimonial on smaller screens
        spaceBetween: 10,
      },
      768: {
        slidesPerView: 2, // Show only one testimonial on smaller screens
        spaceBetween: 10,
      },
      992: {
        slidesPerView: 2, // Show two testimonials on medium screens
        spaceBetween: 20,
      },
    },
  });

  new Swiper(".testimonials-slider", {
    speed: 600,
    loop: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
    slidesPerView: "4",
    pagination: {
      el: ".swiper-pagination",
      type: "bullets",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      100: {
        slidesPerView: 1, // Show only one testimonial on smaller screens
        spaceBetween: 10,
      },
      520: {
        slidesPerView: 2, // Show only one testimonial on smaller screens
        spaceBetween: 10,
      },
      768: {
        slidesPerView: 3, // Show only one testimonial on smaller screens
        spaceBetween: 10,
      },
      992: {
        slidesPerView: 4, // Show two testimonials on medium screens
        spaceBetween: 20,
      },
    },
  });
 
  /**
   * Animation on scroll
   */
  window.addEventListener("load", () => {
    AOS.init({
      duration: 1000,
      easing: "ease-in-out",
      once: true,
      mirror: false,
    });
  });
})();
