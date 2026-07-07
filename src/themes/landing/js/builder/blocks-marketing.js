Vvveb.Blocks.add("krost/marketing-contact-us", {
  name: "Marketing Contact Us",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/marketing-contact-us.png",
  html: /*html*/ `
  <div class="th-get-in-touch-form">
    <div class="form-container th-cf-wrapper overlap th-bg-cf-white">
      <form>
        <div class="form-group">
          <label for="email">Email* (required)</label>
          <input type="email" id="email" class="form-control" placeholder="Email*" required="">
        </div>
        <div class="form-group">
          <label for="company">Company Name* (required)</label>
          <input type="text" id="company" class="form-control" placeholder="Company Name*" required="">
        </div>
        <div class="form-group">
          <label for="full-name">Full Name* (required)</label>
          <input type="text" id="full-name" class="form-control" placeholder="Full Name*" required="">
        </div>
        <div class="form-group">
          <label for="type">Type</label>
          <select id="type" class="form-control">
            <option value="" disabled="" selected="">Type</option>
            <option value="option1">Option 1</option>
            <option value="option2">Option 2</option>
            <option value="option3">Option 3</option>
          </select>
        </div>
        <!-- File Upload -->
        <div class="upload-section">
          <p>Drag and Drop or Click to Upload File</p>
          <input type="file" class="file-input">
        </div>
        <!-- Text Area -->
        <div class="form-group">
          <label for="message">Add Text</label>
          <div class="text-section">
            <textarea class="form-control" placeholder="Enter your message"></textarea>
          </div>
        </div>
        <!-- Submit Button -->
        <div class="form-group">
          <button type="submit" class="th-btn-primary mt-30">
            Submit <span>↗</span>
          </button>
        </div>
      </form>
    </div>
  </div>
  `,
});

Vvveb.Blocks.add("krost/marketing-what-happining-item", {
  name: "Marketing What Happining Item",
  image:
    Vvveb.themeBaseUrl + "screenshots/blocks/marketing-what-happining-item.png",
  html: /*html*/ `
  <div class="row align-items-center th-right-items">
    <div class="col-md-7 col-sm-7 th-underline-tag">
      <p class="th-underline">Bayside City Council - Youth Centre Opening</p>
    </div>
    <div class="col-md-5 col-sm-5">
      <div class="upper-img">
        <img src="/img/blog-page/upper img.png" alt="Bayside City Council">
      </div>
    </div>
  </div>
`,
});
Vvveb.Blocks.add("krost/marketing-subscribe", {
  name: "Marketing Subscribe",
  image: Vvveb.themeBaseUrl + "screenshots/blocks/marketing-subscribe.png",
  html: /*html*/ `
  <div class="th-subscription pl-50 pl-md-0">
    <div class="th-subscription-heading">
      <div class="th-subscription-icon">
        <i class="fa-thin fa-envelope"></i>
      </div>
      <div class="th-subscription-details">
        <div class="sub-title m-0">Get krost product updates</div>
        <div class="description">Receive the latest news &amp; updates from Krost</div>
      </div>
    </div>
    <div class="th-subscription-form">
      <label class="pr-15">
        <input type="text" placeholder="Your Email Address Please">
      </label>
      <div class="link">
        <div class="link-text pr-5">Subscribe Now</div>
        <div class="link-icon">
          <i class="fa-regular fa-arrow-right"></i>
        </div>
      </div>
    </div>
  </div>
`,
});

Vvveb.Blocks.add("krost/marketing-navigation-footer", {
  name: "Marketing Navigation Footer",
  image:
    Vvveb.themeBaseUrl + "screenshots/blocks/marketing-navigation-footer.png",
  html: /*html*/ `
  <div class="th-footer-navigation">
    <ul>
      <li class="pl-0">
        <span class="link pl-0">
          <a href="#"> Our Store</a>
        </span>
      </li>
      <li class="">
        <span class="link">
          <a href="#"> Visit Us</a>
        </span>
      </li>
      <li class="border-right-0">
        <span class="link border-right-0">
          <a href="#"> Contact Us</a>
        </span>
      </li>
    </ul>
  </div>
`,
});
Vvveb.BlocksGroup["Krost Marketing Blocks"] = [
  "krost/marketing-contact-us",
  "krost/marketing-what-happining-item",
  "krost/marketing-subscribe",
  "krost/marketing-navigation-footer",
];
