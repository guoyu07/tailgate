import React from 'react'

class EditDateButton extends React.Component {
  constructor() {
    super()
  }
  render() {
    return (
      <button
        className="pull-right btn btn-sm btn-primary"
        id={this.props.buttonId}
        onClick={this.props.handleClick}><i className="fa fa-calendar"></i>&nbsp;Edit</button>
    )
  }
}

EditDateButton.defaultProps = {buttonId: null, handleClick: null}

EditDateButton.propTypes = {
  buttonId : React.PropTypes.string,
  handleClick: React.PropTypes.func
}

export default EditDateButton
