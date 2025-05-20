from bs4 import BeautifulSoup

def clean_html(file_path, output_path):
    # Read the messy HTML
    with open(file_path, 'r', encoding='utf-8') as file:
        html_content = file.read()
    
    # Parse it with BeautifulSoup
    soup = BeautifulSoup(html_content, 'html.parser')
    
    # Remove unnecessary inline styles, classes, and IDs
    for tag in soup.find_all(True):  # True matches all tags
        if 'style' in tag.attrs:
            del tag.attrs['style']
        if 'class' in tag.attrs:
            del tag.attrs['class']
        if 'id' in tag.attrs:
            del tag.attrs['id']
    
    # Prettify the cleaned HTML
    cleaned_html = soup.prettify()
    
    # Write the cleaned HTML to a new file
    with open(output_path, 'w', encoding='utf-8') as file:
        file.write(cleaned_html)
    
    print(f"Cleaned HTML saved to {output_path}")

# Usage example
input_file = 'govno.html'  # Replace with your file path
output_file = 'cleaned.html'  # Replace with your desired output path
clean_html(input_file, output_file)
