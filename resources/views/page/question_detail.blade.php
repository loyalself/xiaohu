<div ng-controller="QuestionDetailController" class="container question-detail">

    <div class="card">
        <h1>[: Question.current_question.title :]</h1>
        <div class="desc">[:Question.current_question.desc:]</div>
        <div>
            <span class="gray">回答数:[:Question.current_question.answers_with_user_info.length:]</span>
        </div>
        <div class="hr"></div>
        <div class="feed item clearfix">
            <div ng-if="!Question.current_answer_id ||
                          Question.current_answer_id == item.id"
                 ng-repeat="item in Question.current_question.answers_with_user_info">
                <div class="vote">
                    <div ng-click="Question.vote({id:item.id,vote:1})" class="up">赞[: item.upvote_count :]</div>
                    <div ng-click="Question.vote({id:item.id,vote:2})" class="down">踩[: item.downvote_count :]</div>
                </div>
                <div class="feed-item-content">
                    <div>
                        <span ui-sref="user({id:item.user.id})">[:item.user.username:]:</span>
                    </div>
                    <div>[:item.content:]
                        <div class="gray">
                            {{--下面这个链接就是问题加专门的回答跳转链接
                                参数就是跟base.js里面是一样的, id?answer_id  --}}
                            <a ui-sref="question.detail({id:Question.current_question.id,answer_id:item.id})">
                                [:item.updated_at:]
                            </a>
                        </div>
                    </div>
                </div>
                <div class="hr"></div>
            </div>
        </div>
        <div>
            [:Answer.answer_form:]
            <form name="answer_form" class="answer_from">
                <div class="input-group">
                    <textarea type="text"
                           name="content"
                           ng-minlength="5"
                           ng-maxlength="255"
                           ng-model="Answer.answer_form.title"
                              required>
                    </textarea>
                </div>
                <div class="input-group">
                    {{--button[type=submit] 然后按Tab键,这是快捷写法--}}
                    <button class="primary" type="submit">提交</button>
                </div>
            </form>
        </div>
    </div>
</div>