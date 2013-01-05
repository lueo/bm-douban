package bgpack
{
    import com.greensock.*;
    import com.greensock.easing.*;
    import flash.display.*;
    import flash.events.*;
    import flash.net.*;
    import flash.text.*;
    import flash.utils.*;

    dynamic public class MainTimeline extends MovieClip
    {
        public var vStageScale:Number;
        public var xmlLoader:URLLoader;
        public var tStageWidth:int;
        public var vStageWidth:int;
        public var totalPhoto:Number;
        public var imageArray:Array;
        public var testText:TextField;
        public var requestProduct:Number;
        public var vStageHeight:int;
        public var tStageHeight:int;
        public var imageNumberEnd:Number;
        public var xmladdress:String;
        public var imageNumberStart:Number;

        public function MainTimeline()
        {
            addFrameScript(0, this.frame1);
            return;
        }

        function frame1()
        {
            stage.scaleMode = StageScaleMode.NO_SCALE;
            stage.align = StageAlign.TOP_LEFT;
            this.testText = new TextField();
 	        this.xmladdress = "bg.xml";
            this.requestProduct = this.loaderInfo.parameters.pid;
            this.xmlLoader = new URLLoader();
            this.imageArray = new Array();
            this.imageNumberStart = 0;
            this.imageNumberEnd = 0;
            this.show_id = 0;
            this.remove_id = 0;
            this.tStageWidth = new int();
            this.tStageHeight = new int();
            this.vStageWidth = this.loaderInfo.parameters.tsweight;
            this.vStageHeight = this.loaderInfo.parameters.tsheight;
            this.vStageScale = this.vStageWidth / this.vStageHeight;
            this.totalPhoto = new Number();
            this.xmlLoader.load(new URLRequest(this.xmladdress));
            this.xmlLoader.addEventListener(Event.COMPLETE, this.showXML);
            return;
        }

        public function showXML(param1:Event) : void
        {
            XML.ignoreWhitespace = true;
            var img_xml:* = new XML(param1.target.data);
            this.totalPhoto = img_xml.picture.length();
            var temp_mc:* = new MovieClip();
			this.remove_id = this.totalPhoto;
            temp_mc.name = "p" + this.remove_id;
            addChild(temp_mc);
            var i:Number;
            for (i=0;i < this.totalPhoto;i++)
            {
                // label
               this.imageArray[i] = img_xml.picture[i].pid.text();
            }
            trace("load XML Complete");
            this.doLoadingImage();
            return;
        }

        public function doLoadingImage()
        {
            var imgloader:Loader;
            var addphoto2stage:Function;
            addphoto2stage = 
function (param1:Event) : void
{
    var _loc_2:* = param1.target.content;
    _loc_2.smoothing = true;
    var _loc_3:* = new Number(_loc_2.width / _loc_2.height);
    var _loc_4:* = new Number();
    if (_loc_3 >= vStageScale)
    {
        _loc_2.height = vStageHeight;
        _loc_2.scaleX = _loc_2.scaleY;
        _loc_2.x = (-(_loc_2.width - vStageWidth)) / 2;
        testText.text = String("y=" + _loc_2.scaleY);
    }
    else
    {
        _loc_2.width = vStageWidth;
        _loc_2.scaleY = _loc_2.scaleX;
        _loc_2.y = (-(_loc_2.height - vStageHeight)) / 2;
        testText.text = String("x=" + _loc_2.scaleX);
    }// end else if
    TweenLite.to(imgloader, 2, {alpha:1, ease:Quart.easeInOut, onComplete:GetTimer});
    return;
}
;
            imgloader = new Loader();
            imgloader.name = "p" + this.show_id;
            imgloader.load(new URLRequest(this.imageArray[this.show_id]));
            imgloader.contentLoaderInfo.addEventListener(Event.COMPLETE, addphoto2stage);
trace("add  " + imgloader.name);
            addChild(imgloader);
            imgloader.x = 0;
            imgloader.y = 0;
            imgloader.alpha = 0;
            return;
        }

        public function LoadNextImage(param1:TimerEvent)
        {
            trace("remove p" + this.remove_id);
            removeChild(this.getChildByName("p" + this.remove_id));
			
            if (this.remove_id >=Number( this.totalPhoto -1 )){
                this.remove_id = 0;
            }else{
                this.remove_id ++;
			}
			
            if (this.show_id >= Number( this.totalPhoto -1 ) ){
                this.show_id = 0;
            }else{
                this.show_id ++;
            }// end else if
			
            this.doLoadingImage();
            return;
        }

        public function GetTimer()
        {
            var _loc_1:* = new Timer(8000,1);
            _loc_1.addEventListener(TimerEvent.TIMER, this.LoadNextImage);
            _loc_1.start();
            return;
        }
    }
}
