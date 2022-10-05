from bs4 import BeautifulSoup

def loadNumbers3Data():
    result = []
    with open('./Numbers3Result.html', encoding='UTF-8') as f:
        html = f.read()
        soup = BeautifulSoup(html, "html.parser")
        elems = soup.find_all("td", class_='text-center text-bold')
        for el in elems:
            numbers = []
            for num in list(el.text):
                numbers.append(int(num))
            
            result.append(numbers)

    print(result)
    return result
