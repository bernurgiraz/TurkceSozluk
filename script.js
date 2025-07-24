document.addEventListener("DOMContentLoaded", function () {
  const aramaInput = document.getElementById("arama");

  aramaInput.addEventListener("input", function () {
    const query = this.value;
    if (query.length < 2) {
      closeSuggestions();
      return;
    }

    fetch(`suggest.php?q=${encodeURIComponent(query)}`)
      .then(res => res.json())
      .then(data => {
        showSuggestions(data);
      });
  });

  function showSuggestions(words) {
    closeSuggestions();

    const list = document.createElement("ul");
    list.className = "autocomplete-suggestions";

    words.forEach(word => {
      const item = document.createElement("li");
      item.textContent = word;
      item.addEventListener("click", function () {
        window.location.href = `?arama=${encodeURIComponent(word)}&suggest=1`;
      });
      list.appendChild(item);
    });

    document.querySelector(".container").appendChild(list);
  }

  function closeSuggestions() {
    const oldList = document.querySelector(".autocomplete-suggestions");
    if (oldList) oldList.remove();
  }

  document.addEventListener("click", function (e) {
    if (!e.target.closest(".autocomplete-suggestions") && e.target.id !== "arama") {
      closeSuggestions();
    }
  });
});
