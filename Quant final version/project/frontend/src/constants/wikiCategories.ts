export const WIKI_CATEGORY_MAP: Record<string, {
  wikiCategory: string,
  altCategories: string[],
  searchTerms: string[]
}> = {
  "Science": { wikiCategory: "Category:Science", altCategories: ["Category:Scientific_disciplines", "Category:Fields_of_science"], searchTerms: ["science", "scientific", "physics", "chemistry", "biology", "astronomy", "research", "quantum"] },
  "Technology": { wikiCategory: "Category:Technology", altCategories: ["Category:Computing", "Category:Electronics"], searchTerms: ["technology", "computer", "software", "hardware", "internet", "digital", "programming", "artificial intelligence"] },
  "History": { wikiCategory: "Category:History", altCategories: ["Category:Historical_events", "Category:History_by_period"], searchTerms: ["history", "historical", "ancient", "medieval", "civilization", "war", "dynasty", "empire"] },
  "Arts": { wikiCategory: "Category:Arts", altCategories: ["Category:Visual_arts", "Category:Art_by_type"], searchTerms: ["art", "artistic", "painting", "sculpture", "museum", "gallery", "artist", "renaissance"] },
  "Geography": { wikiCategory: "Category:Geography", altCategories: ["Category:Regions", "Category:Countries"], searchTerms: ["geography", "geographic", "country", "continent", "mountain", "river", "ocean", "terrain"] },
  "Sports": { wikiCategory: "Category:Sports", altCategories: ["Category:Ball_games", "Category:Olympic_sports"], searchTerms: ["sport", "sports", "football", "soccer", "basketball", "tennis", "olympic", "championship"] },
  "Entertainment": { wikiCategory: "Category:Entertainment", altCategories: ["Category:Mass_media", "Category:Popular_culture"], searchTerms: ["entertainment", "movie", "film", "cinema", "actor", "actress", "television", "hollywood"] },
  "Politics": { wikiCategory: "Category:Politics", altCategories: ["Category:Government", "Category:Political_science"], searchTerms: ["politics", "political", "government", "election", "president", "parliament", "democracy", "policy"] },
  "Philosophy": { wikiCategory: "Category:Philosophy", altCategories: ["Category:Philosophical_concepts", "Category:Philosophers"], searchTerms: ["philosophy", "philosophical", "ethics", "logic", "metaphysics", "existence", "consciousness", "morality"] },
  "Literature": { wikiCategory: "Category:Literature", altCategories: ["Category:Books", "Category:Fiction"], searchTerms: ["literature", "literary", "book", "novel", "author", "writer", "fiction", "poetry"] },
  "Mathematics": { wikiCategory: "Category:Mathematics", altCategories: ["Category:Mathematical_concepts", "Category:Algebra"], searchTerms: ["mathematics", "mathematical", "algebra", "geometry", "calculus", "theorem", "equation", "number theory"] },
  "Biology": { wikiCategory: "Category:Biology", altCategories: ["Category:Organisms", "Category:Life_sciences"], searchTerms: ["biology", "biological", "organism", "species", "evolution", "genetics", "cell", "ecology"] }
}; 