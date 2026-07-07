Vvveb.Blocks.add("krost/book-now-selected-member", {
    classes: ["th-booking-selected-member"],
    image: Vvveb.themeBaseUrl + "screenshots/blocks/book-now-member.png",
    html: /*html*/`
    <div class="th-booking-selected-member d-flex align-items-center mb-20">
      <div class="th-booking-member-avatar">
        <img src="/${Vvveb.themeBaseUrl}img/contact/member-avatar.png" alt="Member Avatar">
      </div>
      <div class="th-member-info pb-0">
        <p class="th-member-position">Meet With</p>
        <p class="th-member-name">Devon Lane</p>
      </div>
    </div>`,
    name: "Book Now Member",
    htmlAttr: "class"
  });
Vvveb.Blocks.add("krost/book-now-form", {
    classes: ["th-booking-selected-member"],
    image: Vvveb.themeBaseUrl + "screenshots/blocks/book-now-form.png",
    html: /*html*/`
    <form>
        <div class="th-input-group">
            <label for="choose-members">Member</label>
            <div class="choices" data-type="select-one" tabindex="0" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"><div class="choices__inner"><select class="form-control th-choices-select choices__input" name="choose-members" id="choose-members" placeholder="This is a placeholder" hidden="" tabindex="-1" data-choice="active">
            <option value="superman" selected="">Superman</option>
            <option value="batman">Batman</option>
            <option value="galactus">Galactus</option>
            <option value="spawn">Spawn</option>
            </select><div class="choices__list choices__list--single" role="listbox"><div class="choices__item choices__item--selectable" data-item="" data-id="1" data-value="superman" aria-selected="true" role="option">Superman</div></div></div><div class="choices__list choices__list--dropdown" aria-expanded="false"><input type="search" class="choices__input choices__input--cloned" autocomplete="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" aria-label="Member" placeholder=""><div class="choices__list" role="listbox"><div id="choices--choose-members-item-choice-2" class="choices__item choices__item--choice choices__item--selectable is-highlighted" role="option" data-choice="" data-id="2" data-value="batman" data-select-text="Press to select" data-choice-selectable="" aria-selected="true">Batman</div><div id="choices--choose-members-item-choice-3" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="3" data-value="galactus" data-select-text="Press to select" data-choice-selectable="">Galactus</div><div id="choices--choose-members-item-choice-4" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="4" data-value="spawn" data-select-text="Press to select" data-choice-selectable="">Spawn</div><div id="choices--choose-members-item-choice-1" class="choices__item choices__item--choice is-selected choices__item--selectable" role="option" data-choice="" data-id="1" data-value="superman" data-select-text="Press to select" data-choice-selectable="">Superman</div></div></div></div>
        </div>
        <div class="th-input-group">
            <label for="choose-location">Location</label>
            <div class="choices" data-type="select-one" tabindex="0" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"><div class="choices__inner"><select class="form-control th-choices-select choices__input" name="choose-members" id="choose-location" placeholder="This is a placeholder" hidden="" tabindex="-1" data-choice="active">
            <option value="superman" selected="">Superman</option>
            <option value="batman">Batman</option>
            <option value="galactus">Galactus</option>
            <option value="spawn">Spawn</option>
            </select><div class="choices__list choices__list--single" role="listbox"><div class="choices__item choices__item--selectable" data-item="" data-id="1" data-value="superman" aria-selected="true" role="option">Superman</div></div></div><div class="choices__list choices__list--dropdown" aria-expanded="false"><input type="search" class="choices__input choices__input--cloned" autocomplete="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" aria-label="Location" placeholder=""><div class="choices__list" role="listbox"><div id="choices--choose-location-item-choice-2" class="choices__item choices__item--choice choices__item--selectable is-highlighted" role="option" data-choice="" data-id="2" data-value="batman" data-select-text="Press to select" data-choice-selectable="" aria-selected="true">Batman</div><div id="choices--choose-location-item-choice-3" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="3" data-value="galactus" data-select-text="Press to select" data-choice-selectable="">Galactus</div><div id="choices--choose-location-item-choice-4" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="4" data-value="spawn" data-select-text="Press to select" data-choice-selectable="">Spawn</div><div id="choices--choose-location-item-choice-1" class="choices__item choices__item--choice is-selected choices__item--selectable" role="option" data-choice="" data-id="1" data-value="superman" data-select-text="Press to select" data-choice-selectable="">Superman</div></div></div></div>
        </div>
        <div class="th-input-group">
            <label for="choose-tour-type">Tour Type</label>
            <div class="choices" data-type="select-one" tabindex="0" role="combobox" aria-autocomplete="list" aria-haspopup="true" aria-expanded="false"><div class="choices__inner"><select class="form-control th-choices-select choices__input" name="choose-members" id="choose-tour-type" placeholder="This is a placeholder" hidden="" tabindex="-1" data-choice="active">
            <option value="superman" selected="">Superman</option>
            <option value="batman">Batman</option>
            <option value="galactus">Galactus</option>
            <option value="spawn">Spawn</option>
            </select><div class="choices__list choices__list--single" role="listbox"><div class="choices__item choices__item--selectable" data-item="" data-id="1" data-value="superman" aria-selected="true" role="option">Superman</div></div></div><div class="choices__list choices__list--dropdown" aria-expanded="false"><input type="search" class="choices__input choices__input--cloned" autocomplete="off" autocapitalize="off" spellcheck="false" role="textbox" aria-autocomplete="list" aria-label="Tour Type" placeholder=""><div class="choices__list" role="listbox"><div id="choices--choose-tour-type-item-choice-2" class="choices__item choices__item--choice choices__item--selectable is-highlighted" role="option" data-choice="" data-id="2" data-value="batman" data-select-text="Press to select" data-choice-selectable="" aria-selected="true">Batman</div><div id="choices--choose-tour-type-item-choice-3" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="3" data-value="galactus" data-select-text="Press to select" data-choice-selectable="">Galactus</div><div id="choices--choose-tour-type-item-choice-4" class="choices__item choices__item--choice choices__item--selectable" role="option" data-choice="" data-id="4" data-value="spawn" data-select-text="Press to select" data-choice-selectable="">Spawn</div><div id="choices--choose-tour-type-item-choice-1" class="choices__item choices__item--choice is-selected choices__item--selectable" role="option" data-choice="" data-id="1" data-value="superman" data-select-text="Press to select" data-choice-selectable="">Superman</div></div></div></div>
        </div>
    </form>`,
    name: "Book Now From",
    htmlAttr: "class"
});

Vvveb.BlocksGroup['Krost Contacts'] = [
    "krost/book-now-selected-member", 
    "krost/book-now-form"
];