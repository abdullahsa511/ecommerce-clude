export function masonaryOnChange(
  node,
  value,
  input,
  templatePath,
  dataUrl,
  config
) {
  let grid = node.querySelector(".th-masonry-grid");
  grid.innerHTML = "";
  let apiCalls = async () => {
    try {
      const [templateResponse, dataResponse] = await Promise.all([
        fetch(templatePath),
        fetch(dataUrl),
      ]);
      const template = await templateResponse.text();
      const jsonData = await dataResponse.json();

      for (let i = 0; i < value; i++) {
        const itemObject = {
          span: config[i].span,
          item: config[i].item,
          data: jsonData[i],
          baseUrl: Vvveb.themeBaseUrl,
        };
        console.log("item object", itemObject);
        let item = Mustache.render(template, itemObject);
        item = new DOMParser().parseFromString(item, "text/html").body
          .firstChild;
        grid.appendChild(item);
      }

      node.innerHTML = "";
      node.appendChild(grid);
    } catch (error) {
      console.error("Error fetching template or JSON:", error);
    }
  };
  apiCalls();
}
