Vvveb.Sections.add("blog/whats-happening", {
  name: "Whats Happening",
  image: Vvveb.themeBaseUrl + "/screenshots/sections/blogs/whats-happening.png",
  html: `
  <section class="whats-happening-background pt-30">
    <div class="container th-container">
      <div class="th-breadcrumb">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb ">

            <li class="breadcrumb-item  text-dark">

              <a href="/" class="text-dark">
                Home
              </a>


            </li>
            <li class="breadcrumb-item  active text-dark">


              <span class="active text-dark" aria-current="page">
                Blogs
              </span>

            </li>

          </ol>
        </nav>
      </div>
      <div class="section-body mt-25">
        <div class="row">
          <!-- Left Side -->
          <div class="col-lg-7 col-md-12">
            <div class=" whats-happening">
              <h1>WHAT'S HAPPENING?</h1>
            </div>
            <div class="whats-happening-left mt-50" style="background-image: url('/${Vvveb.themeBaseUrl}img/blog-page/whats-happening-left.png');">
              <div class="th-member-info-container">
                <div class="link read-more th-white-link">
                  <div class="link-text pr-15 font-weight-600 font-white">Read More</div>
                  <div class="link-icon-btn-white-large">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </div>
                </div>
              </div>
            </div>
            <!-- Content below the image -->
            <div class="whats-happening-content">
              <h2>Embracing Human-Centric Design: A Path to Enhanced Usability and Experience</h2>
              <p>Sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.</p>
            </div>
          </div>

          <!-- Right Side -->
          <div class="col-lg-5 col-md-12 th-whats-happening-right">
            <div class="whats-happening-right">
              <div class="row align-items-center th-right-items">
                <div class="col-md-7 col-sm-7 th-underline-tag">
                  <p class="th-underline">Bayside City Council - Youth Centre Opening</p>
                </div>
                <div class="col-md-5 col-sm-5">
                  <div class="upper-img">
                      <img src="/${Vvveb.themeBaseUrl}img/blog-page/upper-img.png" alt="Bayside City Council">
                  </div>
                </div>
              </div>
              <div class="row align-items-center th-right-items">
                <div class="col-md-7 col-sm-7 th-underline-tag">
                  <p class="th-underline">Kenni - A Class Of Its Own</p>
                </div>
                <div class="col-md-5 col-sm-5">
                  <div class="upper-img">
                    <img src="/${Vvveb.themeBaseUrl}img/blog-page/middle-img.png" alt="Kenni">
                  </div>
                </div>
              </div>
              <div class="row align-items-center th-right-items">
                <div class="col-md-7 col-sm-7 th-underline-tag">
                  <p class="th-underline">An Arena For Innovative Collaboration</p>
                </div>
                <div class="col-md-5 col-sm-5">
                  <div class="upper-img">
                    <img src="/${Vvveb.themeBaseUrl}img/blog-page/lower-img.png" alt="Arena">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>`,
});
Vvveb.Sections.add("blog/latest-news", {
  name: "Latest News",
  image: Vvveb.themeBaseUrl + "/screenshots/sections/blogs/latest-news.png",
  html: `
  <section id="projects-list" class="latest-news">
    <div class="container th-container">
      <div class="row">

        <div class="th-section-header " style="display: block">
          <div class="th-section-header-wrapper text-center">
            <h2 class="  th-section-title "> Latest News </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

        </div>


        <div class="section-body">
          <div class="row">
            <div class="col-12 col-sm-6 col-md-4 mb-4 project-list-gap">
              <div class="list-img">
                <div class="img-container">
                  <img src="/${Vvveb.themeBaseUrl}img/blog-page/News1.png" alt="" />
                </div>
                <div class="add-to-pinboard position-absolute top-right-30">
                  <i class="fa-solid fa-plus"></i>
                </div>
              </div>
              <div class="card-footer">
                <h3 class="font-weight-400">Krost's Sydney Office Update With 3d Tour</h3>
                <div class="link th-read-more-btn">
                  <div class="link-text pr-5">Read More</div>
                  <div class="link-icon-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4 project-list-gap">
              <div class="list-img">
                <div class="img-container">
                  <img src="/${Vvveb.themeBaseUrl}img/blog-page/News2.png" alt="" />
                </div>
                <div class="add-to-pinboard position-absolute top-right-30">
                  <i class="fa-solid fa-plus"></i>
                </div>
              </div>
              <div class="card-footer">
                <h3 class="font-weight-400">THE BOOTH: WHERE IDEAS AND CONNECTIONS GROW</h3>
                <div class="link th-read-more-btn">
                  <div class="link-text pr-5">Read More</div>
                  <div class="link-icon-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4 project-list-gap">
              <div class="list-img">
                <div class="img-container">
                  <img src="/${Vvveb.themeBaseUrl}img/blog-page/News3.png" alt="" />
                </div>
                <div class="add-to-pinboard position-absolute top-right-30">
                  <i class="fa-solid fa-plus"></i>
                </div>
              </div>
              <div class="card-footer">
                <h3 class="font-weight-400">Unveiling Our Updated Sydney Showroom</h3>
                <div class="link th-read-more-btn">
                  <div class="link-text pr-5">Read More</div>
                  <div class="link-icon-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4 project-list-gap">
              <div class="list-img">
                <div class="img-container">
                  <img src="/${Vvveb.themeBaseUrl}img/blog-page/News4.png" alt="" />
                </div>
                <div class="add-to-pinboard position-absolute top-right-30">
                  <i class="fa-solid fa-plus"></i>
                </div>
              </div>
              <div class="card-footer">
                <h3 class="font-weight-400">Beyond Aesthetics: How Organic Design</h3>
                <div class="link th-read-more-btn">
                  <div class="link-text pr-5">Read More</div>
                  <div class="link-icon-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4 project-list-gap">
              <div class="list-img">
                <div class="img-container">
                  <img src="/${Vvveb.themeBaseUrl}img/blog-page/News5.png" alt="" />
                </div>
                <div class="add-to-pinboard position-absolute top-right-30">
                  <i class="fa-solid fa-plus"></i>
                </div>
              </div>
              <div class="card-footer">
                <h3 class="font-weight-400">Watch The Full Panel Discussion On Designing</h3>
                <div class="link th-read-more-btn">
                  <div class="link-text pr-5">Read More</div>
                  <div class="link-icon-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 mb-4 project-list-gap">
              <div class="list-img">
                <div class="img-container">
                  <img src="/${Vvveb.themeBaseUrl}img/blog-page/News6.png" alt="" />
                </div>
                <div class="add-to-pinboard position-absolute top-right-30">
                  <i class="fa-solid fa-plus"></i>
                </div>
              </div>
              <div class="card-footer">
                <h3 class="font-weight-400">Extraordinary Sydney Showroom Event Hosted</h3>
                <div class="link th-read-more-btn">
                  <div class="link-text pr-5">Read More</div>
                  <div class="link-icon-btn">
                    <i class="fa-regular fa-arrow-up degree-60"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="d-flex justify-content-center">
                <a href="#" class="th-btn-gray text-capitalize mt-50">
                  <span class="mr-5">
                    View All
                  </span>
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>`,
});
Vvveb.Sections.add("blog/home-blogs", {
  name: "Home Blogs",
  image: Vvveb.themeBaseUrl + "/screenshots/sections/blogs/home-blogs.png",
  commands: [
    {
      execute: function () {
        createSlider("th-blog-slider");
      },
    },
  ],
  html: `
  <section id="home-blogs" class="bg-gray">
    <div class="container th-container">
      <div class="row">

        <div class="th-section-header ">
          <div class="th-section-header-wrapper left flex-1">
            <h2 class="  th-section-title "> Blogs </h2>
            <div class="th-section-subtitle" style="font-size: ">
              Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
            </div>
          </div>

          <div class="right">
            <div class="th-section-header-link">
              <span class="th-section-header-link-text All ProjectsClass">All Projects</span>
              <span class="th-section-header-link-btn">
                <i class="fa-regular fa-arrow-up degree-60"></i>
              </span>
            </div>
          </div>

        </div>

        <div class="section-body">
          <div class="row">
            <div class="col-md-12">
              <div class="swiper th-home-blog-slider th-blog-slider">
                <div class="swiper-wrapper">
                  <div class="swiper-slide">
                    <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_blog-1.jpg" />
                    <div class="add-to-pinboard position-absolute top-right-30">
                      <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="slider-footer">

                      <h6 class="th-title-20 font-weight-700 th-blog-slider-title">Lorem ipsum</h6>
                      <p class="th-blog-slider-description">
                        Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                      </p>

                      <div class="link">
                        <div class="link-text pr-5">Read More</div>
                        <div class="link-icon-btn">
                          <i class="fa-regular fa-arrow-up degree-60"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_blog-2.jpg" />
                    <div class="add-to-pinboard position-absolute top-right-30">
                      <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="slider-footer">

                      <h6 class="th-title-20 font-weight-700 th-blog-slider-title">Lorem ipsum</h6>
                      <p class="th-blog-slider-description">
                        Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                      </p>

                      <div class="link">
                        <div class="link-text pr-5">Read More</div>
                        <div class="link-icon-btn">
                          <i class="fa-regular fa-arrow-up degree-60"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_blog-3.jpg" />
                    <div class="add-to-pinboard position-absolute top-right-30">
                      <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="slider-footer">

                      <h6 class="th-title-20 font-weight-700 th-blog-slider-title">Lorem ipsum</h6>
                      <p class="th-blog-slider-description">
                        Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                      </p>

                      <div class="link">
                        <div class="link-text pr-5">Read More</div>
                        <div class="link-icon-btn">
                          <i class="fa-regular fa-arrow-up degree-60"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_blog-4.jpg" />
                    <div class="add-to-pinboard position-absolute top-right-30">
                      <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="slider-footer">

                      <h6 class="th-title-20 font-weight-700 th-blog-slider-title">Lorem ipsum</h6>
                      <p class="th-blog-slider-description">
                        Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                      </p>

                      <div class="link">
                        <div class="link-text pr-5">Read More</div>
                        <div class="link-icon-btn">
                          <i class="fa-regular fa-arrow-up degree-60"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="swiper-slide">
                    <img src="/${Vvveb.themeBaseUrl}img/bg/home/home_blog-5.jpg" />
                    <div class="add-to-pinboard position-absolute top-right-30">
                      <i class="fa-solid fa-plus"></i>
                    </div>
                    <div class="slider-footer">

                      <h6 class="th-title-20 font-weight-700 th-blog-slider-title">Lorem ipsum</h6>
                      <p class="th-blog-slider-description">
                        Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.
                      </p>

                      <div class="link">
                        <div class="link-text pr-5">Read More</div>
                        <div class="link-icon-btn">
                          <i class="fa-regular fa-arrow-up degree-60"></i>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="swiper-scrollbar"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>`,
});

Vvveb.Sections.add("blog/blog-main", {
  name: "Blog Main",
  image: Vvveb.themeBaseUrl + "/screenshots/sections/blogs/blog-main.png",
  html: /*html*/`
  <section id="th-blog-main" class="pt-0">
    <div class="row th-blog-main-header">
      <div class="col-md-12">
      </div>
    </div>
    <div class="container th-container">
      <div class="section-body">
        <div class="row">
          <div class="col-lg-6 pr-50 th-blog-main-content">
            <div class="th-section-header" style="display: block">
              <div class="th-section-header-wrapper ">
                <h2 class="pt-35 pb-20 "> Project Details </h2>
                <div class="th-section-subtitle" style="font-size:  20px ">
                  Pellentesque sodales lacinia diam, vel lacinia lectus ultrices id. Maecenas viverra justo nec mauris pulvinar dignissim. Proin quis arcu eu velit vehicula fermentum. Ut pharetra, nunc sed fringilla fermentum, nulla eros rhoncus felis, ac varius ligula quam nec nisl. Proin ac tellus sed dui ultrices tempus. Duis ut arcu et diam lacinia cursus nec non justo
                </div>
              </div>

            </div>

            <p>
              Aliquam malesuada tortor ut dolor suscipit, id convallis ipsum lacinia. Curabitur feugiat lectus non sem ullamcorper, nec finibus nulla consequat.
            </p>

          </div>
          <div class="col-lg-6">
            <div class="th-img-container blog-detail">
              <img src="/${Vvveb.themeBaseUrl}img/blog-detail/blog-main.png" alt="Main Blog image">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <p class="pt-100">
              Sed nec odio magna. Aliquam non libero ultricies, efficitur nulla id, ultricies urna. Nullam at placerat justo, non fermentum magna. Nullam nec libero odio. Sed eleifend euismod tellus, ut fermentum libero pellentesque a. Sed eget nulla et mi consectetur vestibulum. Phasellus at leo eu orci vestibulum tincidunt. Vestibulum fringilla mi id felis varius ultricies. Donec nec suscipit nulla. Vivamus vitae diam purus.
            </p>
            <p>
              Sed nec odio magna. Aliquam non libero ultricies, efficitur nulla id, ultricies urna. Nullam at placerat justo, non fermentum magna. Nullam nec libero odio. Sed eleifend euismod tellus, ut fermentum libero pellentesque a. Sed eget nulla et mi consectetur vestibulum. Phasellus at leo eu orci vestibulum tincidunt. Vestibulum fringilla mi id felis varius ultricies. Donec nec suscipit nulla. Vivamus vitae diam purus.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  `
})

Vvveb.SectionsGroup["Blogs"] = [
  "blog/whats-happening",
  "blog/latest-news",
  "blog/home-blogs",
  "blog/blog-main",
];
