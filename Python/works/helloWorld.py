# ハローワールド　クラス
class Printer:
    message = ''
    def __init__(self, message):
        self.message = message

    def printMessage(self):
        print(self.message)


# ハローワールド function
def printHelloWorld():
    print('HelloWorld')

printer = Printer('success')
printer.printMessage()
