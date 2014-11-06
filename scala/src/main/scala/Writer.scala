

/*
object Test extends App {
	val writer = Writer("this is a file.txt")
	writer.println("Hi")
	
	writer.println { puts =>
		List("one", "two", "three").foreach { number => puts(number) }
	}
}
*/
trait SimplePrintWriter {
	
	import java.io._
	
	def uri: String
	
	private def getPW() = {
		val file = new java.io.File(uri)
		val parent = file.getParentFile()
		
		if(parent != null) file.getParentFile().mkdirs()
		if(!file.exists())	file.createNewFile()
		
		new PrintWriter(new BufferedWriter(new FileWriter(file, true)))
	}
	def print(data: String) = {
		val writer = getPW()
		writer.print(data)
		writer.close()
	}
	
	def println(data: String) = {
		val writer = getPW()
		writer.println(data)
		writer.flush
	}
	
	def print(func: ((String) => Unit) => Unit) {
		val writer = getPW()
		func(s => writer.print(s))
		writer.close()
	}
	
	def println(func: ((String) => Unit) => Unit) {
		val writer = getPW()
		func(s => writer.println(s))
		writer.close()
	}
	
	def toBuffer = new LazyBuffer(this)
	
	def apply(data: String) = this.println(data)
	
	def apply(func: ((String) => Unit) => Unit) = this.println(func)
}

case class Writer(val uri: String) extends SimplePrintWriter 

/*
object bufferTest extends App {
	val writer = Writer("hello world.txt")
	val buff = writer.toBuffer
	buff("much")
	buff("wow")
	buff.done
}
 */
sealed class LazyBuffer(lzFile: SimplePrintWriter) {
	private val buf = new StringBuffer
	
	def append(data: Any) = {
		buf.append(data)
		this
	}
	
	def +=(data: Any) = {
		buf.append(data)
		this
	}
	
	def done = {
		lzFile.print(buf.toString)
		lzFile
	}
	
	def apply(data: Any) = append(data)
}