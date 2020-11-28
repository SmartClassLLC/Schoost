<?php

/*
 * This file is part of Schoost.
 *
 * (c) SmartClass, LLC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schoost\Feed;

class Feed 
{
    private $feedsTable = _FEEDS_;
    private $feedImagesTable = _FEED_IMAGES_;
    private $feedAudiosTable = _FEED_AUDIOS_;
    private $feedDocsTable = _FEED_DOCS_;
    private $feedRelatedTable = _FEED_RELATED_;
    private $feedTagsTable = _FEED_TAGS_;
    private $feedVideosTable = _FEED_VIDEOS_;
    private $feedCommentsTable = _FEED_COMMENTS_;
    
    private $Id;
    
    private $sourceId;
    
    private $feedType;
    
    private $title;
    
    private $shortText;
    
    private $longText;
    
    private $url;
    
    private $coverPicture;
    
    private $liked;
    
    private $chatChannelLink;
    
    private $commentsOn;
    
    private $dueAt;
    
    private $posterPicture;
    
    private $posterBy;
    
    private $posterAt;
    
    private $comment;
    
    private $userId;
    
    function __construct() {
        //parent::__construct();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->Id;
    }

    /**
     * @param mixed $Id
     */
    public function setId($Id)
    {
        $this->Id = $Id;
    }

    /**
     * @return mixed
     */
    public function getSourceId()
    {
        return $this->sourceId;
    }

    /**
     * @param mixed $sourceId
     */
    public function setSourceId($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    /**
     * @return mixed
     */
    public function getFeedType()
    {
        return $this->feedType;
    }

    /**
     * @param mixed $feedType
     */
    public function setFeedType($feedType)
    {
        $this->feedType = $feedType;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getShortText()
    {
        return $this->shortText;
    }

    /**
     * @param mixed $shortText
     */
    public function setShortText($shortText)
    {
        $this->shortText = $shortText;
    }

    /**
     * @return mixed
     */
    public function getLongText()
    {
        return $this->longText;
    }

    /**
     * @param mixed $longText
     */
    public function setLongText($longText)
    {
        $this->longText = $longText;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getCoverPicture()
    {
        return $this->coverPicture;
    }

    /**
     * @param mixed $coverPicture
     */
    public function setCoverPicture($coverPicture)
    {
        $this->coverPicture = $coverPicture;
    }

    /**
     * @return mixed
     */
    public function getLiked()
    {
        return $this->liked;
    }

    /**
     * @param mixed $liked
     */
    public function setLiked($liked)
    {
        $this->liked = $liked;
    }

    /**
     * @return mixed
     */
    public function getChatChannelLink()
    {
        return $this->chatChannelLink;
    }

    /**
     * @param mixed $chatChannelLink
     */
    public function setChatChannelLink($chatChannelLink)
    {
        $this->chatChannelLink = $chatChannelLink;
    }

    /**
     * @return mixed
     */
    public function getCommentsOn()
    {
        return $this->commentsOn;
    }

    /**
     * @param mixed $commentsOn
     */
    public function setCommentsOn($commentsOn)
    {
        $this->commentsOn = $commentsOn;
    }

    /**
     * @return mixed
     */
    public function getDueAt()
    {
        return $this->dueAt;
    }

    /**
     * @param mixed $dueAt
     */
    public function setDueAt($dueAt)
    {
        $this->dueAt = $dueAt;
    }

    /**
     * @return mixed
     */
    public function getPosterPicture()
    {
        return $this->posterPicture;
    }

    /**
     * @param mixed $posterPicture
     */
    public function setPosterPicture($posterPicture)
    {
        $this->posterPicture = $posterPicture;
    }

    /**
     * @return mixed
     */
    public function getPosterBy()
    {
        return $this->posterBy;
    }

    /**
     * @param mixed $posterBy
     */
    public function setPosterBy($posterBy)
    {
        $this->posterBy = $posterBy;
    }

    /**
     * @return mixed
     */
    public function getPosterAt()
    {
        return $this->posterAt;
    }
    
    
    /**
     * @param mixed $posterBy
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }
    
    /**
     * @param mixed $posterBy
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $posterAt
     */
    public function setPosterAt($posterAt)
    {
        $this->posterAt = $posterAt;
    }

    function setFeedsTable($table)
    {
        $this->feedsTable = $table;
    }
    
    function setFeedImagesTable($table)
    {
        $this->feedImagesTable = $table;
    }
    
    function setFeedAudiosTable($table)
    {
        $this->feedAudiosTable = $table;
    }
    
    function setFeedDocsTable($table)
    {
        $this->feedDocsTable = $table;
    }
    
    function setFeedRelatedTable($table)
    {
        $this->feedRelatedTable = $table;
    }
    
    function setFeedTagsTable($table)
    {
        $this->feedTagsTable = $table;
    }
    
    function setFeedVideosTable($table)
    {
        $this->feedVideosTable = $table;
    }
    
    function setFeedCommentsTable($table)
    {
        $this->feedCommentsTable = $table;
    }
    
    function saveFeedData($fileUrl, $feedId)
    {
        global $dbi;
        
        $fileType = self::feedFileType($fileUrl);
        
        if($fileType == "image")
        {
            $data = array(
                "image"     => $fileUrl,
                "feedId"    => $feedId    
            );
            
            $return = $dbi->insert($this->feedImagesTable, $data);    
            
        }
        else if($fileType == "word" || $fileType == "excel" || $fileType == "powerpoint" || $fileType == "pdf")
        {
            $data = array(
                "doc"       => $fileUrl,
                "feedId"    => $feedId    
            );
            
            $return = $dbi->insert($this->feedDocsTable, $data);
        }
        else if($fileType == "video")
        {
            $data = array(
                "video"     => $fileUrl,
                "feedId"    => $feedId    
            );
            
            $return = $dbi->insert($this->feedVideosTable, $data);
        }
        else if($fileType == "audio")
        {
            $data = array(
                "audio"     => $fileUrl,
                "feedId"    => $feedId    
            );
            
            $return = $dbi->insert($this->feedAudiosTable, $data);
        }
        
        return $return;
        
    }
    
    function deleteFeedData($fileUrl, $feedId)
    {
        global $dbi;
        
        $fileType = self::feedFileType($fileUrl);
        
        if($fileType == "image")
        {
            $dbi->where("image", $fileUrl);
            $dbi->where("feedId", $feedId);
            $return = $dbi->delete($this->feedImagesTable);
        }
        else if($fileType == "word" || $fileType == "excel" || $fileType == "powerpoint" || $fileType == "pdf")
        {
            $dbi->where("doc", $fileUrl);
            $dbi->where("feedId", $feedId);
            $return = $dbi->delete($this->feedDocsTable);
        }
        else if($fileType == "video")
        {
            $dbi->where("video", $fileUrl);
            $dbi->where("feedId", $feedId);
            $return = $dbi->delete($this->feedVideosTable);
        }
        else if($fileType == "audio")
        {
            $dbi->where("audio", $fileUrl);
            $dbi->where("feedId", $feedId);
            $return = $dbi->delete($this->feedAudiosTable);
        }
        
        return $return;
        
    }
    
    function deleteFeed()
    {
        global $dbi, $ySubeKodu;
        
        $dbi->where("feedId", $this->Id);
        $deleteImages = $dbi->delete($this->feedImagesTable);
        
        $dbi->where("feedId", $this->Id);
        $deleteDocs = $dbi->delete($this->feedDocsTable);
        
        $dbi->where("feedId", $this->Id);
        $deleteVideos = $dbi->delete($this->feedVideosTable);
        
        $dbi->where("feedId", $this->Id);
        $deleteAudios = $dbi->delete($this->feedAudiosTable);
        
        $dbi->where("feedId", $this->Id);
        $dbi->where("schoolId", $ySubeKodu);
        $updateRelatedTable = $dbi->update($this->feedRelatedTable, array('active' => '0'));
        
        $dbi->where("Id", $this->Id);
        $return = $dbi->delete($this->feedsTable);
        
        return $return;
    }
    
    function saveFeed()
    {
        global $dbi, $ySubeKodu, $simsDateTime;
        
        $data = array(
            "sourceId"          => $this->sourceId,
            "feedType"          => $this->feedType,
            "title"             => $this->title,
            "shortText"         => $this->shortText,
            "longText"          => $this->longText,
            "url"               => $this->url,
            "coverPicture"      => $this->coverPicture,
            "liked"             => $this->liked,
            "chatChannelLink"   => $this->chatChannelLink,
            "commentsOn"        => $this->commentsOn,
            "dueAt"             => $this->dueAt,
            "posterPicture"     => $this->posterPicture,
            "posterBy"          => $this->posterBy,
            "posterAt"          => $this->posterAt
        );
        
        $return = $dbi->insert($this->feedsTable, $data);
        
        if($return)
        {
            $relatedTableData = array(
                'userId'    => $this->userId,
                'feedId'    => $return,
                'schoolId'  => $ySubeKodu,
                'active'    => '1',
                'dateAt'    => $simsDateTime
            );
            
            $dbi->insert($this->feedRelatedTable, $relatedTableData);
        }
        
        return $return;
        
    }
    
    function updateFeed()
    {
        global $dbi;
        
        $data = array(
            "feedType"          => $this->feedType,
            "title"             => $this->title,
            "longText"          => $this->longText,
            "url"               => $this->url,
            "posterPicture"     => $this->posterPicture,
        );
        
        $dbi->where("Id", $this->Id);
        $return = $dbi->update($this->feedsTable, $data);
        
        return $return;
        
    }
    
    function feedLiked()
    {
        global $dbi;
        
        $dbi->where("Id", $this->Id);
        $nofLiked = $dbi->getOne($this->feedsTable);
        
        $data = array(
            "liked" => $nofLiked["liked"] + 1,
        );
        
        $dbi->where("Id", $this->Id);
        $return = $dbi->update($this->feedsTable, $data);
        
        return $return;
        
    }
    
    function feedUnliked()
    {
        global $dbi;
        
        $dbi->where("Id", $this->Id);
        $nofLiked = $dbi->getOne($this->feedsTable);
        
        $data = array(
            "liked" => $nofLiked["liked"] - 1,
        );
        
        $dbi->where("Id", $this->Id);
        $return = $dbi->update($this->feedsTable, $data);
        
        return $return;
        
    }
    
    function saveFeedComment()
    {
        global $dbi, $simsDateTime;
        
        $data = array(
            'feed_id'       => $this->Id,
            'comment'       => $this->comment,
            'user_id'       => $this->userId,
            'created_at'    => $simsDateTime
        );
        
        $result = $dbi->insert($this->feedCommentsTable, $data);
        
        return $result;
                
    }
    
    function deleteFeedComment()
    {
        global $dbi;
        
        $dbi->where("feed_id", $this->Id);
        $dbi->where("comment", $this->comment);
        $dbi->where("user_id", $this->userId);
        $result = $dbi->delete($this->feedCommentsTable);
        
        return $result;
                
    }
    
    function feedFileType($filename)
    {
        $filename = explode(".", $filename);
    	switch ($filename[sizeof($filename) - 1])
    	{
    		case 'png': 
    		case 'jpg': 
    		case 'jpeg': 
    		case 'gif': 
    			return "image"; 
    			break;
    
    		case 'doc': 
    		case 'docx': 
    			return "word"; 
    			break;
    
    		case 'xls': 
    		case 'xlsx': 
    			return "excel"; 
    			break;
    
    		case 'ppt': 
    		case 'pptx': 
    		case 'pps': 
    			return "powerpoint"; 
    			break;
    
    		case 'mpg': 
    		case 'mpeg':
    		case 'mp4':
    		case 'ogg':
    			return "video"; 
    			break;
    
    		case 'mp3':
    		case 'wav':
    			return "audio"; 
    			break;
    
    		case 'pdf': 
    			return "pdf"; 
    			break;
    
    		case 'zip':
    		case 'rar':
    			return "zip"; 
    			break;
    
    		case 'txt': 
    			return "text"; 
    			break;
    		
    		default:
    			return "file";
    			break;
    
        }
    }
}
